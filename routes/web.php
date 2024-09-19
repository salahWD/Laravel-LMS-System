<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\TestAttemptController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BookedAppointmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\GoogleCalendarController;
use App\Models\BookedAppointment;
use Google\Service\AndroidPublisher\UserComment;

// barryvdh/laravel-debugbar

Route::middleware('localizationRedirect')->group(function () {

  Route::get('/', [PageController::class, 'home'])->name("home");

  Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('category_show');
  Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('article_show');
  Route::get('/articles', [PageController::class, 'articles'])->name('articles_show');
  Route::get('/tags/{tag:slug}', [TagController::class, 'show'])->name('tag_view');
  Route::get('/courses', [CourseController::class, 'show_all'])->name('courses_show');
  Route::POST('/articles/{article}', [CommentController::class, 'store'])->name('comment_article');
  Route::get('/products', [ProductController::class, 'index'])->name("shop");
  Route::get('/products/{product}', [ProductController::class, 'show'])->name("product_show");
  Route::get('/collection/{category}', [CategoryController::class, 'show'])->name("product_category_show");
  Route::get('/contact-us', [PageController::class, 'contactus'])->name('contact_us');
  Route::POST('/contact-us', [MessageController::class, 'store']);

  // Route::POST('/checkout', [CartController::class, 'checkout'])->name('checkout');
  Route::get('/thanks', [CartController::class, 'success'])->name('checkout_success');
  Route::get('/track/{order:token}', [OrderController::class, 'show'])->name('order_tracking');

  Route::middleware(['auth'])->group(function () {
    Route::prefix('/me')->group(function () {
      Route::get('/orders', [OrderController::class, 'index'])->name('my_orders');
      Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
      Route::get('/certificates', [ProfileController::class, 'certificates'])->name('profile.certificates');
      Route::get('/settings', [ProfileController::class, 'settings'])->name('profile.settings');
      Route::get('/meetings', [ProfileController::class, 'meetings'])->name('profile.meetings');
      Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
      Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
      Route::get('/meetings/{bookedAppointment}', [BookedAppointmentController::class, 'show'])->name("booked_appointment_show");
    });
    Route::controller(CertificateController::class)->group(function () {
      Route::get('/certificates', 'index')->name('certificates');
      Route::get('/certificate/{certificate}', 'download')->name('certificate_download');
      Route::get('/certificate/theme/{theme}', 'show_theme')->name('certificate_theme_show');
    });
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('course_show');
    Route::get('/courses/{course}/enroll', [CourseController::class, 'enroll'])->name('course_enroll');
    Route::get('/lectures/{lecture}', [LectureController::class, 'show'])->name('lecture_show');
    Route::get('/tests/{test}', [TestController::class, 'show'])->name('test_show');
    Route::get('/appointments/{appointment:url}', [AppointmentController::class, 'show'])->name("appointment_booking");
    Route::POST('/appointments/{bookedAppointment}', [BookedAppointmentController::class, 'update'])->name("booked.appointmet.notes");
    Route::get('/{user:username}/meetings', [UserController::class, 'show_meetings'])->name("user_appointments");
    Route::controller(CartController::class)->group(function () {
      Route::get('/cart', 'show')->name('cart_show');
      Route::get('/cart/edit', 'edit')->name('cart_edit');
      Route::POST('/cart', 'update')->name('cart_update');
    });
  });

  Route::prefix('/dashboard')->middleware(['auth', 'admin'])->group(function () {
    Route::view('/', 'dashboard')->name('dashboard');
    Route::controller(Dashboard::class)->group(function () {
      Route::get('/articles', 'articles')->name('articles_manage');
      Route::get('/categories', 'categories')->name('categories_manage');
      Route::get('/courses', 'courses')->name('courses_manage');
    });
    Route::prefix('/users')->group(function () {
      Route::get('/', [Dashboard::class, 'users'])->name('users_manage');
      Route::get('/create', [UserController::class, 'create'])->name('user_create');
      Route::POST('/create', [UserController::class, 'store']);
      Route::get('/{user}', [UserController::class, 'edit'])->name('user_edit');
      Route::POST('/{user}', [UserController::class, 'update']);
    });
    Route::controller(ArticleController::class)->group(function () {
      Route::get('/articles/create', 'create')->name('article_create');
      Route::POST('/articles/create', 'store');
      Route::get('/articles/{article}', 'edit')->name('article_edit');
      Route::POST('/articles/{article}', 'update');
    });
    Route::controller(CategoryController::class)->group(function () {
      Route::get('/categories/create', [CategoryController::class, 'create'])->name('category_create');
      Route::POST('/categories/create', [CategoryController::class, 'store']);
      Route::get('/categories/{category}', [CategoryController::class, 'edit'])->name('category_edit');
      Route::POST('/categories/{category}', [CategoryController::class, 'update']);
    });
    Route::controller(CourseController::class)->group(function () {
      Route::get('/courses/create', 'create')->name('course_create');
      Route::POST('/courses/create', 'store');
      Route::get('/courses/{course}', 'edit')->name('course_edit');
      Route::POST('/courses/{course}', 'update');
      Route::get('/courses/{course}/delete', 'destroy')->name("course_delete");
    });
    Route::get('/courses/{course}/lecture', [LectureController::class, 'create_for'])->name('lecture_create_for');
    Route::get('/lectures', [Dashboard::class, 'lectures'])->name('lectures_manage');
    Route::get('/lectures/create', [LectureController::class, 'create'])->name('lecture_create');
    Route::POST('/lectures/create', [LectureController::class, 'store']);
    Route::get('/lectures/{lecture}', [LectureController::class, 'edit'])->name('lecture_edit');
    Route::POST('/lectures/{lecture}', [LectureController::class, 'update']);
    Route::get('/comments', [Dashboard::class, 'comments'])->name('comments_manage');
    Route::POST('/comments/{comment}', [CommentController::class, 'update']);
    Route::get('/tags', [Dashboard::class, 'tags'])->name('tags_manage');
    Route::get('/tags/{tag}', [Dashboard::class, 'tag'])->name('tag_edit');
    Route::POST('/tags/{tag}', [TagController::class, 'update']);
    Route::get('/certificates', [Dashboard::class, 'certificates'])->name('certificates_manage');
    Route::get('/certificates/create', [CertificateController::class, 'create'])->name('certificate_create');
    Route::POST('/certificates/create', [CertificateController::class, 'store']);
    Route::get('/certificate/{certificate}', [CertificateController::class, 'show'])->name('certificate_show');
    Route::get('/certificates/{certificate}', [CertificateController::class, 'edit'])->name('certificate_edit');
    Route::POST('/certificates/{certificate}', [CertificateController::class, 'update']);
    Route::POST('/certificates/{certificate}/delete', [CertificateController::class, 'destroy'])->name("certificate_delete");
    Route::get('/tests', [TestController::class, 'index'])->name('tests_manage');
    Route::get('/tests/create', [TestController::class, 'create'])->name('test_create');
    Route::get('/courses/{course}/tests/create', [TestController::class, 'create'])->name('course_create_test');
    Route::get('/tests/{test}/bulid', [TestController::class, 'build'])->name('test_build');
    Route::get('/tests/{test}/report', [TestController::class, 'report'])->name('test_report');
    Route::get('/tests/{test}/export', [TestController::class, 'export'])->name('test_export_data');
    Route::POST('/tests/create', [TestController::class, 'store'])->name("test_store");
    Route::POST('/tests/{test}', [TestController::class, 'update']);
    Route::prefix('/products')->group(function () {
      Route::get('/', [Dashboard::class, 'products'])->name("products_manage");
      Route::get('/create', [ProductController::class, 'create'])->name("product_create");
      Route::POST('/create', [ProductController::class, 'store']);
      Route::get('/{product}/edit', [ProductController::class, 'edit'])->name("product_edit");
      Route::POST('/{product}/edit', [ProductController::class, 'update']);
      Route::get('/{product}/delete', [ProductController::class, 'destroy'])->name("product_delete");
      Route::get('/categories', [CategoryController::class, 'collection_index'])->name("product_category");
      Route::get('/categories/create', [CategoryController::class, 'collection_create'])->name("product_category_create");
      Route::POST('/categories/create', [CategoryController::class, 'store']);
    });
    Route::prefix('/orders')->group(function () {
      Route::get('/', [Dashboard::class, 'orders'])->name("orders_manage");
      Route::POST('/', [OrderController::class, 'store']);
      Route::get('/{order}', [OrderController::class, 'edit'])->name("order_edit");
      Route::POST('/{order}', [OrderController::class, 'update']);
      Route::get('/{order}/refund', [OrderController::class, 'destroy'])->name("order_refund");
    });
    Route::prefix('/appointments')->group(function () {
      Route::get('/', [AppointmentController::class, 'index'])->name("appointments_manage");
      Route::get('/create', [AppointmentController::class, 'create'])->name("appointment_create");
      Route::POST('/create', [AppointmentController::class, 'store']);
      Route::get('/export', [BookedAppointmentController::class, 'export'])->name("booked_appointment_export");
      Route::get('/{appointment}', [AppointmentController::class, 'edit'])->name("appointment_edit");
      Route::POST('/{appointment}', [AppointmentController::class, 'update']);
      Route::POST('/{appointment}/status', [AppointmentController::class, 'status']);
      Route::delete('/{appointment}', [AppointmentController::class, 'destroy'])->name("appointment_delete");
    });
    Route::prefix('/bookedAppointments')->group(function () {
      Route::delete('/{bookedAppointment}', [BookedAppointmentController::class, 'destroy'])->name("booked_appointment_delete");
    });
    Route::get('/settings', [Dashboard::class, 'settings'])->name("dashboard_settings");
    Route::POST('/settings', [Dashboard::class, 'set_settings']);
  });

  require __DIR__ . '/auth.php';
});

Route::prefix('/app-request')->group(function () {

  Route::POST('/cart/{product}', [ProductController::class, 'add_to_cart'])->name("add_product_cart");
  Route::POST('/webhook', [WebhookController::class, 'webhook'])->name('webhook');

  Route::POST('/test/{test}/answering', [TestAttemptController::class, 'answering'])->name("attempt_answering");
  Route::POST('/test/{test}/form', [TestAttemptController::class, 'formEntry'])->name("attempt_form_entry");
  Route::get('/question/{question}', [QuestionController::class, 'show'])->name('show_question');
  Route::POST('/test/attempts', [TestAttemptController::class, 'store'])->name("attempt_create");

  // google calendar reoutes
  // Route::get('/calendar-auth', [GoogleCalendarController::class, 'authenticate'])->name('google.auth');
  Route::get('/calendar-callback', [GoogleCalendarController::class, 'handleGoogleCallback'])->name('google.callback');
  // Route::get('/calendar-auth', [GoogleCalendarController::class, 'redirectToGoogle']);

  Route::middleware('auth')->group(function () {
    Route::POST('/lectures/{lecture}', [LectureController::class, 'ajax_done'])->name("lecture_done_ajax");
    Route::POST('/appointments/{appointment:url}', [BookedAppointmentController::class, 'store'])->name("appointment_booking_store");
    Route::get('/appointments/{appointment:url}/unavailable', [BookedAppointmentController::class, 'list_booked']);
  });
});

Route::prefix('/app-request')->middleware(['auth', 'admin'])->group(function () {

  Route::POST('/tags/{tag}/delete', [TagController::class, 'api_destroy']);
  Route::POST('/comments/report/{comment}', [CommentController::class, 'report']);
  Route::POST('/comments/{comment}/approve', [CommentController::class, 'approve']);
  Route::POST('/comments/{comment}/delete', [CommentController::class, 'api_destroy']);
  Route::POST('/categories/{category}/delete', [CategoryController::class, 'api_destroy']);
  Route::POST('/lectures/{lecture}/delete', [LectureController::class, 'api_destroy']);
  Route::POST('/articles/{article}/delete', [ArticleController::class, 'api_destroy']);
  Route::POST('/products/{product}/delete', [ProductController::class, 'api_destroy']);
  Route::get('/proxy', [ProductController::class, 'proxy'])->name("product_proxy");
  Route::POST('/articles/upload-attachment', [ArticleController::class, 'upload_attachment'])->name("article_attachment");
  Route::POST('/courseitems/order', [CourseController::class, 'reorder_items'])->name("courseitem_order");


  // Route::POST('/tests/{test}/question', [ArticleController::class, 'upload_attachment'])->name("article_attachment");
  Route::POST('/test/{test}/name', [TestController::class, "update_name"])->name("test_edit_name");
  Route::POST('/test/{test}/intro', [TestController::class, "update"])->name("test_update");
  Route::POST('/test/{test}/image/delete', [TestController::class, "delete_intro_image"])->name("test_intro_image_delete");
  Route::POST('/tests/{test}/add_question', [QuestionController::class, 'store'])->name("add_question");
  Route::POST('/tests/{test}/delete', [TestController::class, "destroy"]);
  Route::POST('/tests/{test}/reorder', [QuestionController::class, 'reorder'])->name("reorder_test_questions");
  Route::POST('/question/{question}/delete', [QuestionController::class, 'destroy']);
  Route::POST('/question/{question}/copy', [QuestionController::class, 'copy']);
  Route::POST('/question/{question}/reorder', [AnswerController::class, 'reorder'])->name("reorder_question_answers");
  Route::POST('/question/{question}', [QuestionController::class, 'update'])->name('update_question');
  Route::POST('/question/{question}/image_actions', [QuestionController::class, 'image_actions']);
  Route::POST('/tests/{test}/result', [ResultController::class, 'store'])->name("result");
  // Route::get('/tests/{test}/result', [ResultController::class, 'show']);
  Route::POST('/tests/{test}/result/destroy', [ResultController::class, 'destroy'])->name("test_delete_result");
  Route::get('/certificate/{certificate}', [CertificateController::class, 'show_api'])->name('certificate_show_api');
  Route::POST('/certificate/{certificate}', [CertificateController::class, 'update_api']);
  Route::POST('/tests/{test}/certificate', [TestController::class, 'certificates'])->name('test_certificate');
  Route::POST('/tests/{test}/certificate/delete', [TestController::class, 'certificate_delete'])->name('test_certificate_delete');

  Route::get('/attempts/{testAttempt}', [TestAttemptController::class, 'show'])->name("attempt_get");
});


// https://imgaz3.staticbg.com/thumb/large/oaupload/banggood/images/4F/32/productId // full image
// https://imgaz.staticbg.com/thumb/list_grid/oaupload/banggood/images/4F/32/productId // small image
