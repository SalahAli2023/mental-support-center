<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\ProgramSessionController;
use App\Http\Controllers\Api\SessionActivityController;
use App\Http\Controllers\Api\FrontendProgramController;
use App\Http\Controllers\Api\PhaseController;
use App\Http\Controllers\Api\HomeworkController;
use App\Http\Controllers\Api\ProgramProgressController;
use App\Http\Controllers\Api\UserProgramController;
use App\Http\Controllers\Api\SessionCompletionController;
use App\Http\Controllers\Api\ActivitySubmissionController;


Route::middleware('auth:sanctum')->group(function () {
    // تتبع البرامج (للمشرفين)
    Route::get('/admin/enrollments', [UserProgramController::class, 'index']);

    // البرامج
    Route::prefix('programs')->group(function () {
    Route::get('/', [ProgramController::class, 'index']);
    Route::post('/', [ProgramController::class, 'store']);
    Route::get('/{id}', [ProgramController::class, 'show']);
    Route::put('/{id}', [ProgramController::class, 'update']);
    Route::delete('/{id}', [ProgramController::class, 'destroy']);
    Route::patch('/{id}/status', [ProgramController::class, 'changeStatus']);
    Route::get('/{id}/sessions', [ProgramController::class, 'sessions']);
    Route::get('/{id}/users', [ProgramController::class, 'users']);
    
    // المراحل (Phases)
    Route::prefix('{programId}/phases')->group(function () {
        Route::get('/', [PhaseController::class, 'index']);
        Route::post('/', [PhaseController::class, 'store']);
        Route::post('/reorder', [PhaseController::class, 'reorder']);
        Route::get('/{id}', [PhaseController::class, 'show']);
        Route::put('/{id}', [PhaseController::class, 'update']);
        Route::delete('/{id}', [PhaseController::class, 'destroy']);
    });
    
    // التقييمات (Assessments)
    Route::prefix('{programId}/assessments')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ProgramAssessmentController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\ProgramAssessmentController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\ProgramAssessmentController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\ProgramAssessmentController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\ProgramAssessmentController::class, 'destroy']);
        Route::post('/{id}/submit-result', [\App\Http\Controllers\Api\ProgramAssessmentController::class, 'submitResult']);
        Route::get('/results/{userId?}', [\App\Http\Controllers\Api\ProgramAssessmentController::class, 'getUserResults']);
    });
    
    // التقدم (Progress)
    Route::prefix('{programId}/progress')->group(function () {
        Route::get('/', [ProgramProgressController::class, 'getUserProgress']);
        Route::get('/activities/{activityId}/status', [ProgramProgressController::class, 'checkActivityStatus']);
        Route::post('/activities/{activityId}/start', [ProgramProgressController::class, 'startActivity']);
        Route::post('/activities/{activityId}/complete', [ProgramProgressController::class, 'completeActivity']);
    });
});

// الجلسات
// تغيير من 'sessions' إلى 'program-sessions'
Route::prefix('program-sessions')->group(function () {
    Route::get('/', [ProgramSessionController::class, 'index']);
    Route::post('/', [ProgramSessionController::class, 'store']);
    Route::get('/{id}', [ProgramSessionController::class, 'show']);
    Route::put('/{id}', [ProgramSessionController::class, 'update']);
    Route::delete('/{id}', [ProgramSessionController::class, 'destroy']);
    
    // المهام المنزلية (Homework)
    Route::prefix('{sessionId}/homework')->group(function () {
        Route::get('/', [HomeworkController::class, 'index']);
        Route::post('/', [HomeworkController::class, 'store']);
        Route::post('/reorder', [HomeworkController::class, 'reorder']);
        Route::get('/{id}', [HomeworkController::class, 'show']);
        Route::put('/{id}', [HomeworkController::class, 'update']);
        Route::delete('/{id}', [HomeworkController::class, 'destroy']);
        Route::post('/{id}/submit', [HomeworkController::class, 'submit']);
        Route::post('/{homeworkId}/submissions/{submissionId}/review', [HomeworkController::class, 'review']);
    });
});

// الأنشطة
Route::prefix('activities')->group(function () {
    Route::get('/', [SessionActivityController::class, 'index']);
    Route::post('/', [SessionActivityController::class, 'store']);
    Route::get('/{id}', [SessionActivityController::class, 'show']);
    Route::put('/{id}', [SessionActivityController::class, 'update']);
    Route::delete('/{id}', [SessionActivityController::class, 'destroy']);
    Route::patch('/{id}/toggle-mandatory', [SessionActivityController::class, 'toggleMandatory']);
    Route::get('/statistics/{sessionId}', [SessionActivityController::class, 'statistics']);
});
// في ملف routes/api.php داخل مجموعة auth:sanctum
Route::prefix('user-programs')->group(function () {
    Route::get('/', [UserProgramController::class, 'index']);
    Route::post('/', [UserProgramController::class, 'store']);
    Route::get('/statistics', [UserProgramController::class, 'statistics']);
    Route::get('/my-enrollments', [UserProgramController::class, 'myEnrollments']);
    Route::post('/enroll', [UserProgramController::class, 'enroll']);
    
    Route::get('/{id}', [UserProgramController::class, 'show']);
    Route::put('/{id}', [UserProgramController::class, 'update']);
    Route::delete('/{id}', [UserProgramController::class, 'destroy']);
    Route::patch('/{id}/status', [UserProgramController::class, 'changeStatus']);
    Route::patch('/{id}/progress', [UserProgramController::class, 'updateProgress']);
    Route::post('/{id}/unenroll', [UserProgramController::class, 'unenroll']);
});
// في ملف routes/api.php داخل مجموعة auth:sanctum
Route::prefix('session-completions')->group(function () {
    Route::get('/', [SessionCompletionController::class, 'index']);
    Route::post('/', [SessionCompletionController::class, 'store']);
    Route::get('/statistics', [SessionCompletionController::class, 'statistics']);
    Route::get('/my-completions', [SessionCompletionController::class, 'myCompletions']);
    
    Route::get('/{id}', [SessionCompletionController::class, 'show']);
    Route::put('/{id}', [SessionCompletionController::class, 'update']);
    Route::delete('/{id}', [SessionCompletionController::class, 'destroy']);
    
    // العمليات الخاصة بالجلسات
    Route::post('/sessions/{sessionId}/complete', [SessionCompletionController::class, 'completeSession']);
    Route::delete('/sessions/{sessionId}/complete', [SessionCompletionController::class, 'uncompleteSession']);
    Route::get('/sessions/{sessionId}/check', [SessionCompletionController::class, 'checkCompletion']);
});




// روتات تقديمات الأنشطة
Route::prefix('activity-submissions')->name('activity-submissions.')->group(function () {
    
    // عرض جميع التقديمات (للمشرفين والمستخدمين)
    Route::get('/', [ActivitySubmissionController::class, 'index'])
        ->name('index');
    
    // إنشاء تقديم جديد
    Route::post('/', [ActivitySubmissionController::class, 'store'])
        ->name('store');
    
    // عرض تقديم محدد
    Route::get('/{id}', [ActivitySubmissionController::class, 'show'])
        ->name('show');
    
    // تحديث تقديم
    Route::put('/{id}', [ActivitySubmissionController::class, 'update'])
        ->name('update');
    
    // حذف تقديم
    Route::delete('/{id}', [ActivitySubmissionController::class, 'destroy'])
        ->name('destroy');
    
    // تقديم نشاط (للمستخدم)
    Route::post('/submit', [ActivitySubmissionController::class, 'submitActivity'])
        ->name('submit');
    
    // إكمال نشاط
    Route::post('/{activityId}/complete', [ActivitySubmissionController::class, 'completeActivity'])
        ->name('complete');
    
    // مراجعة تقديم (للمشرفين)
    Route::post('/{id}/review', [ActivitySubmissionController::class, 'reviewSubmission'])
        ->name('review');
    
    // تقديمات المستخدم الحالي
    Route::get('/user/my-submissions', [ActivitySubmissionController::class, 'mySubmissions'])
        ->name('my-submissions');
    
    // إحصائيات
    Route::get('/statistics', [ActivitySubmissionController::class, 'statistics'])
        ->name('statistics');
    
    // التحقق من تقديم نشاط
    Route::get('/check/{activityId}', [ActivitySubmissionController::class, 'checkSubmission'])
        ->name('check');
    
    // تقديمات حسب النشاط (للمشرفين)
    Route::get('/activity/{activityId}', [ActivitySubmissionController::class, 'byActivity'])
        ->name('by-activity');
});

});




Route::prefix('frontend')->group(function () {
        
        // ==================== البرامج العامة (بدون مصادقة) ====================
        
        // عرض جميع البرامج
        Route::get('/programs', [FrontendProgramController::class, 'index']);
        
        // عرض برنامج محدد
        Route::get('/programs/{id}', [FrontendProgramController::class, 'show']);
        
        // عرض جلسات البرنامج
        Route::get('/programs/{id}/sessions', [FrontendProgramController::class, 'programSessions']);
        
        // معرض صور البرنامج
        Route::get('/programs/{id}/images', [FrontendProgramController::class, 'programImages']);
        
        // ==================== الجلسات والأنشطة (بدون مصادقة) ====================
        
        // عرض جلسة محددة
        Route::get('/programs/{programId}/sessions/{sessionId}', [FrontendProgramController::class, 'showSession']);
        
        // عرض نشاط محدد
        Route::get('/programs/{programId}/sessions/{sessionId}/activities/{activityId}', [FrontendProgramController::class, 'showActivity']);
        
        // ==================== المستخدم (بالمصادقة) ====================
        Route::middleware('auth:sanctum')->group(function () {
            
            // 👤 بيانات المستخدم
            Route::get('/user/my-programs', [FrontendProgramController::class, 'myPrograms']);
            Route::get('/user/statistics', [FrontendProgramController::class, 'userStatistics']);
            
            // ✅ التسجيل والإلغاء
            Route::post('/programs/{programId}/enroll', [FrontendProgramController::class, 'enroll']);
            Route::post('/programs/{programId}/unenroll', [FrontendProgramController::class, 'unenroll']);
            
            // 📈 التقدم والتتبع
            Route::get('/programs/{programId}/progress', [FrontendProgramController::class, 'programProgress']);
            Route::post('/programs/{programId}/update-progress', [FrontendProgramController::class, 'updateProgress']);
            
            // 🎯 إدارة الجلسات
            Route::post('/programs/{programId}/sessions/{sessionId}/complete', [FrontendProgramController::class, 'completeSession']);
            
            // 📝 إدارة الأنشطة
            Route::post('/programs/{programId}/sessions/{sessionId}/activities/{activityId}/submit', [FrontendProgramController::class, 'submitActivity']);
            Route::post('/programs/{programId}/sessions/{sessionId}/activities/{activityId}/complete', [FrontendProgramController::class, 'completeActivity']);
        });
        });