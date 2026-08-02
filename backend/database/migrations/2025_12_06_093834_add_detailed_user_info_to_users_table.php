<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // النوع (ذكر، أنثى)
            $table->enum('gender', ['male', 'female'])->nullable()->after('phone');
            
            // تاريخ الميلاد
            $table->date('date_of_birth')->nullable()->after('gender');
            
            // العنوان
            $table->string('country')->nullable()->after('date_of_birth');
            $table->string('city')->nullable()->after('country');
            $table->string('governorate')->nullable()->after('city');
            $table->string('district')->nullable()->after('governorate');
            
            // الحالة الاجتماعية
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable()->after('district');
            
            // المستوى التعليمي
            $table->enum('education_level', ['elementary', 'middle', 'high_school', 'diploma', 'bachelor', 'graduate'])->nullable()->after('marital_status');
            
            // الوضع الوظيفي
            $table->enum('employment_status', ['student', 'government_employee', 'private_employee', 'unemployed', 'housewife', 'retired'])->nullable()->after('education_level');
            
            // المهنة/مجال العمل (اختياري)
            $table->string('profession')->nullable()->after('employment_status');
            
            // الدخل الشهري
            $table->enum('monthly_income', ['less_than_60k', '61k_to_120k', '121k_to_200k', '201k_to_350k', 'more_than_351k'])->nullable()->after('profession');
            
            // الغرض من استخدام المنصة (JSON array)
            $table->json('platform_purposes')->nullable()->after('monthly_income');
            
            // الموافقة على شروط الخدمة
            $table->boolean('terms_accepted')->default(false)->after('platform_purposes');
            
            // الموافقة على سياسة الخصوصية
            $table->boolean('privacy_accepted')->default(false)->after('terms_accepted');
            
            // إقرار بصحة المعلومات
            $table->boolean('info_accuracy_confirmed')->default(false)->after('privacy_accepted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'gender',
                'date_of_birth',
                'country',
                'city',
                'governorate',
                'district',
                'marital_status',
                'education_level',
                'employment_status',
                'profession',
                'monthly_income',
                'platform_purposes',
                'terms_accepted',
                'privacy_accepted',
                'info_accuracy_confirmed'
            ]);
        });
    }
};
