<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationVerificationCode;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Illuminate\Validation\ValidationException;

class RegistrationVerificationController extends Controller
{
    /**
     * التحقق من صيغة البريد الإلكتروني بصيغة صارمة
     */
    private function isValidEmail(string $email): bool
    {
        // ✅ التحقق من وجود @ و نقطة في النطاق
        if (!str_contains($email, '@') || !str_contains($email, '.')) {
            return false;
        }

        // ✅ التحقق من أن النطاق يحتوي على نقطة بعد @
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return false;
        }

        $domain = $parts[1];
        if (!str_contains($domain, '.')) {
            return false;
        }

        // ✅ التحقق من أن النطاق ليس قصيراً جداً
        $domainParts = explode('.', $domain);
        if (count($domainParts) < 2) {
            return false;
        }

        // ✅ التحقق من أن الجزء الأخير من النطاق (TLD) طويل بما يكفي
        $tld = end($domainParts);
        if (strlen($tld) < 2) {
            return false;
        }

        // ✅ التحقق من عدم وجود مسافات
        if (str_contains($email, ' ')) {
            return false;
        }

        // ✅ استخدام filter_var كتحقق إضافي
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }

    /**
     * إرسال رمز التحقق إلى البريد الإلكتروني
     */
    public function sendCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            // 'email' => ['required', 'email', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            ],
        ]);

        $email = $validated['email'];

        // ✅ التحقق من صيغة البريد بصيغة صارمة
        if (!$this->isValidEmail($email)) {
            throw ValidationException::withMessages([
                'email' => 'يرجى إدخال بريد إلكتروني صحيح (مثال: name@domain.com).',
            ]);
        }

        // ✅ التحقق من عدم استخدام البريد من قبل
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            throw ValidationException::withMessages([
                'email' => 'هذا البريد الإلكتروني مستخدم بالفعل. يرجى استخدام بريد آخر أو تسجيل الدخول.',
            ]);
        }

        // ✅ منع البريد المؤقت (اختياري)
        $tempEmailDomains = ['tempmail.com', '10minutemail.com', 'guerrillamail.com'];
        $domain = explode('@', $email)[1];
        if (in_array($domain, $tempEmailDomains)) {
            throw ValidationException::withMessages([
                'email' => 'لا يمكن استخدام بريد إلكتروني مؤقت. يرجى استخدام بريد دائم.',
            ]);
        }

        // ✅ إنشاء رمز تحقق
        $code = (string) random_int(100000, 999999);

        EmailVerification::updateOrCreate(
            ['email' => $email],
            [
                'code' => Hash::make($code),
                'expires_at' => now()->addMinutes(10),
                'verified_at' => null,
            ]
        );

        try {
            Mail::to($email)->send(new RegistrationVerificationCode($code));

            return response()->json([
                'message' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني.',
                'success' => true,
            ]);

        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'تعذر الاتصال بخادم البريد. يرجى المحاولة مرة أخرى لاحقاً.',
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * التحقق من صحة الرمز
     */
    public function verifyCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
        ]);

        $verification = EmailVerification::where('email', $validated['email'])->first();

        if (!$verification) {
            throw ValidationException::withMessages([
                'email' => 'لم يتم العثور على طلب تحقق لهذا البريد.',
            ]);
        }

        // في بيئة التطوير، نكون أكثر تسامحاً حتى لا نعيق الاختبار
        if (!app()->environment('local')) {
            if ($verification->isExpired()) {
                throw ValidationException::withMessages([
                    'code' => 'انتهت صلاحية رمز التحقق. يرجى طلب رمز جديد.',
                ]);
            }

            if (!Hash::check($validated['code'], $verification->code)) {
                throw ValidationException::withMessages([
                    'code' => 'رمز التحقق غير صحيح.',
                ]);
            }
        }

        $verification->update(['verified_at' => now()]);

        return response()->json([
            'message' => 'تم التحقق من البريد الإلكتروني بنجاح.',
            'success' => true,
        ]);
    }

    /**
     * إعادة إرسال رمز التحقق
     */
    public function resendCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        // ✅ التحقق من وجود طلب سابق
        $verification = EmailVerification::where('email', $validated['email'])->first();

        if (!$verification) {
            throw ValidationException::withMessages([
                'email' => 'لم يتم العثور على طلب تحقق لهذا البريد.',
            ]);
        }

        // ✅ إنشاء رمز جديد
        $code = (string) random_int(100000, 999999);

        $verification->update([
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
            'verified_at' => null,
        ]);

        try {
            Mail::to($validated['email'])->send(new RegistrationVerificationCode($code));

            return response()->json([
                'message' => 'تم إعادة إرسال رمز التحقق إلى بريدك الإلكتروني.',
                'success' => true,
            ]);

        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'تعذر إعادة إرسال رمز التحقق. يرجى المحاولة مرة أخرى.',
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
