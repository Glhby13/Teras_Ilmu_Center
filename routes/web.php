<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UstazController;
use App\Http\Controllers\MuridController;
use App\Http\Controllers\UmumController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KitabController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LivechatController;
use App\Http\Controllers\VideoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


// Route::middleware(['guest'])->group(function () {

// Redirect root URL to /login
Route::get('/', function () {
    return redirect('/login');
});

// Grup route untuk halaman login dan register
Route::middleware('guest')->group(function () {
    // Route untuk halaman login
    Route::get('login', [LoginController::class, 'index'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.post');

    // Route untuk register
    Route::get('register', [RegisterController::class, 'index'])->name('register.index');
    Route::post('register', [RegisterController::class, 'register'])->name('register');
});

// Tangani permintaan GET ke /logout
Route::get('logout', function () {
    return redirect('/login');
})->name('logout.get');

// Route untuk logout
Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Grup route untuk Admin
Route::prefix('admin')->middleware(['auth', 'useraccess:admin', 'preventBackButton', 'disableCaching'])->group(function () {
    Route::get('home', [AdminController::class, 'index'])->name('admin.home');
    ///////////////Ustaz///////////////
    Route::get('ustaz', [AdminController::class, 'dataUstaz'])->name('admin.ustaz');
    Route::post('ustaz', [AdminController::class, 'createUstaz'])->name('admin.createUstaz');
    Route::post('ustaz/delete', [AdminController::class, 'softDeleteUstaz'])->name('admin.softDeleteUstaz');
    Route::post('ustaz/update{id_ustaz}', [AdminController::class, 'updateUstaz'])->name('admin.updateUstaz');
    Route::get('ustaz/trash', [AdminController::class, 'TrashdataUstaz'])->name('admin.trashUstaz');
    Route::post('ustaz/restore', [AdminController::class, 'restoreUstaz'])->name('admin.restoreUstaz');
    Route::post('ustaz/delete/permanent', [AdminController::class, 'DeleteUstaz'])->name('admin.DeleteUstaz');
    ///////////////Murid///////////////
    Route::get('murid', [AdminController::class, 'dataMurid'])->name('admin.murid');
    Route::post('murid', [AdminController::class, 'createMurid'])->name('admin.createMurid');
    Route::post('murid/delete', [AdminController::class, 'softDeleteMurid'])->name('admin.softDeleteMurid');
    Route::post('murid/update{id_murid}', [AdminController::class, 'updateMurid'])->name('admin.updateMurid');
    Route::get('murid/trash', [AdminController::class, 'TrashdataMurid'])->name('admin.trashMurid');
    Route::post('murid/restore', [AdminController::class, 'restoreMurid'])->name('admin.restoreMurid');
    Route::post('murid/delete/permanent', [AdminController::class, 'DeleteMurid'])->name('admin.DeleteMurid');
    ///////////////Umum///////////////
    Route::get('umum', [AdminController::class, 'dataUmum'])->name('admin.umum');
    Route::post('umum', [AdminController::class, 'createUmum'])->name('admin.createUmum');
    Route::post('umum/delete', [AdminController::class, 'softDeleteUmum'])->name('admin.softDeleteUmum');
    Route::post('umum/update{id_umum}', [AdminController::class, 'updateUmum'])->name('admin.updateUmum');
    Route::get('umum/trash', [AdminController::class, 'TrashdataUmum'])->name('admin.trashUmum');
    Route::post('umum/restore', [AdminController::class, 'restoreUmum'])->name('admin.restoreUmum');
    Route::post('umum/delete/permanent', [AdminController::class, 'DeleteUmum'])->name('admin.DeleteUmum');
    ///////////////Report///////////////
    Route::get('report', [AdminController::class, 'dataReport'])->name('admin.report');
    Route::post('report/deletePost', [AdminController::class, 'reportDeletePost'])->name('admin.reportDeletePost');
    Route::post('report/deleteReport', [AdminController::class, 'reportDeleteReport'])->name('admin.reportDeleteReport');
    ///////////////Iklan///////////////
    Route::get('iklan', [AdminController::class, 'dataIklan'])->name('admin.iklan');
    Route::post('iklan', [AdminController::class, 'createIklan'])->name('admin.createIklan');
    Route::post('iklan/delete', [AdminController::class, 'softDeleteIklan'])->name('admin.softDeleteIklan');
    Route::post('iklan/update{id_iklan}', [AdminController::class, 'updateIklan'])->name('admin.updateIklan');
    Route::get('iklan/trash', [AdminController::class, 'TrashdataIklan'])->name('admin.trashIklan');
    Route::post('iklan/restore', [AdminController::class, 'restoreIklan'])->name('admin.restoreIklan');
    Route::post('iklan/delete/permanent', [AdminController::class, 'DeleteIklan'])->name('admin.DeleteIklan');


    // Tambahkan route lain untuk admin di sini
});

// Grup route untuk Ustaz
Route::prefix('ustaz')->middleware(['auth', 'useraccess:ustaz', 'preventBackButton', 'disableCaching'])->group(function () {
    Route::get('home', [UstazController::class, 'index'])->name('ustaz.home');
    Route::get('home/kategori/{kategori?}', [UstazController::class, 'index'])->name('ustaz.homekategori');
    Route::post('follow', [FollowController::class, 'follow'])->name('ustaz.follow');
    Route::delete('follow', [FollowController::class, 'unfollow'])->name('ustaz.unfollow');
    Route::get('profile', [UstazController::class, 'profile'])->name('ustaz.profile');
    Route::get('profile/view/{username}', [UstazController::class, 'viewprofile'])->name('ustaz.viewprofile');
    Route::post('profile/password', [UstazController::class, 'updatePassword'])->name('ustaz.updatePassword');
    Route::post('profile', [UstazController::class, 'updateProfile'])->name('ustaz.updateprofile');
    Route::post('deskripsi', [UstazController::class, 'updateDeskripsi'])->name('ustaz.updatedeskripsi');
    Route::get('kitab', [KitabController::class, 'index'])->name('ustaz.kitab');
    Route::post('vote/{id_question}', [QuestionController::class, 'vote'])->name('ustaz.vote');
    Route::post('question', [QuestionController::class, 'create'])->name('ustaz.createquestion');
    Route::post('editquestion/{id_question}', [QuestionController::class, 'edit'])->name('ustaz.editquestion');
    Route::post('deletequestion/{id_question}', [QuestionController::class, 'delete'])->name('ustaz.hapusquestion');
    Route::post('reportquestion/{id_question}', [QuestionController::class, 'reportQuestion'])->name('ustaz.reportQuestion');
    Route::post('answer/{id_question}', [AnswerController::class, 'create'])->name('ustaz.createanswer');
    Route::post('editanswer/{id_answer}', [AnswerController::class, 'edit'])->name('ustaz.editanswer');
    Route::post('deleteanswer/{id_answer}', [AnswerController::class, 'delete'])->name('ustaz.hapusanswer');
    Route::get('video', [VideoController::class, 'index'])->name('ustaz.video');
    Route::post('video', [VideoController::class, 'create'])->name('ustaz.createvideo');
    Route::post('editvideo/{id_video}', [VideoController::class, 'edit'])->name('ustaz.editvideo');
    Route::post('deletevideo/{id_video}', [VideoController::class, 'delete'])->name('ustaz.hapusvideo');
    Route::get('livechat', [LivechatController::class, 'indexUstaz'])->name('ustaz.livechat');
    Route::get('livechat/chat/{id_livechat}', [LivechatController::class, 'chatUstaz'])->name('ustaz.chat');
    Route::post('/livechat/send', [LivechatController::class, 'sendMessageAjaxUstaz'])->name('livechat.sendUstaz');

    // Tambahkan route lain untuk ustaz di sini
});

// Grup route untuk Murid
Route::prefix('murid')->middleware(['auth', 'useraccess:murid', 'preventBackButton', 'disableCaching'])->group(function () {
    Route::get('home', [MuridController::class, 'index'])->name('murid.home');
    Route::get('home/kategori/{kategori?}', [MuridController::class, 'index'])->name('murid.homekategori');
    Route::post('follow', [FollowController::class, 'follow'])->name('murid.follow');
    Route::delete('follow', [FollowController::class, 'unfollow'])->name('murid.unfollow');
    Route::get('profile', [MuridController::class, 'profile'])->name('murid.profile');
    Route::get('profile/view/{username}', [MuridController::class, 'viewprofile'])->name('murid.viewprofile');
    Route::post('profile/password', [MuridController::class, 'updatePassword'])->name('murid.updatePassword');
    Route::post('profile', [MuridController::class, 'updateProfile'])->name('murid.updateprofile');
    Route::post('deskripsi', [MuridController::class, 'updateDeskripsi'])->name('murid.updatedeskripsi');
    Route::get('kitab', [KitabController::class, 'index'])->name('murid.kitab');
    Route::post('vote/{id_question}', [QuestionController::class, 'vote'])->name('murid.vote');
    Route::post('question', [QuestionController::class, 'create'])->name('murid.createquestion');
    Route::post('editquestion/{id_question}', [QuestionController::class, 'edit'])->name('murid.editquestion');
    Route::post('deletequestion/{id_question}', [QuestionController::class, 'delete'])->name('murid.hapusquestion');
    Route::post('reportquestion/{id_question}', [QuestionController::class, 'reportQuestion'])->name('murid.reportQuestion');
    Route::post('answer/{id_question}', [AnswerController::class, 'create'])->name('murid.createanswer');
    Route::post('editanswer/{id_answer}', [AnswerController::class, 'edit'])->name('murid.editanswer');
    Route::post('deleteanswer/{id_answer}', [AnswerController::class, 'delete'])->name('murid.hapusanswer');
    Route::get('video', [VideoController::class, 'index'])->name('murid.video');
    Route::get('//livechat/ustaz-online', [LivechatController::class, 'getUstazOnline'])->name('murid.ustazOnline');
    Route::get('livechat', [LivechatController::class, 'index'])->name('murid.livechat');
    Route::post('livechat/start', [LivechatController::class, 'startSession'])->name('murid.livechatstart');
    Route::post('/livechat/send', [LivechatController::class, 'sendMessageAjax'])->name('muridlivechat.send');


    // Tambahkan route lain untuk murid di sini
});

// Grup route untuk Umum
Route::prefix('umum')->middleware(['auth', 'useraccess:umum', 'preventBackButton', 'disableCaching'])->group(function () {
    Route::get('home', [UmumController::class, 'index'])->name('umum.home');
    Route::get('home/kategori/{kategori?}', [UmumController::class, 'index'])->name('umum.homekategori');
    Route::post('follow', [FollowController::class, 'follow'])->name('umum.follow');
    Route::delete('follow', [FollowController::class, 'unfollow'])->name('umum.unfollow');
    Route::get('profile', [UmumController::class, 'profile'])->name('umum.profile');
    Route::get('profile/view/{username}', [UmumController::class, 'viewprofile'])->name('umum.viewprofile');
    Route::post('profile/password', [UmumController::class, 'updatePassword'])->name('umum.updatePassword');
    Route::post('profile', [UmumController::class, 'updateProfile'])->name('umum.updateprofile');
    Route::post('deskripsi', [UmumController::class, 'updateDeskripsi'])->name('umum.updatedeskripsi');
    Route::get('kitab', [KitabController::class, 'index'])->name('umum.kitab');
    Route::post('vote/{id_question}', [QuestionController::class, 'vote'])->name('umum.vote');
    Route::post('question', [QuestionController::class, 'create'])->name('umum.createquestion');
    Route::post('editquestion/{id_question}', [QuestionController::class, 'edit'])->name('umum.editquestion');
    Route::post('deletequestion/{id_question}', [QuestionController::class, 'delete'])->name('umum.hapusquestion');
    Route::post('reportquestion/{id_question}', [QuestionController::class, 'reportQuestion'])->name('umum.reportQuestion');
    Route::post('answer/{id_question}', [AnswerController::class, 'create'])->name('umum.createanswer');
    Route::post('editanswer/{id_answer}', [AnswerController::class, 'edit'])->name('umum.editanswer');
    Route::post('deleteanswer/{id_answer}', [AnswerController::class, 'delete'])->name('umum.hapusanswer');
    Route::get('video', [VideoController::class, 'index'])->name('umum.video');
    Route::get('//livechat/ustaz-online', [LivechatController::class, 'UmumgetUstazOnline'])->name('umum.ustazOnline');
    Route::get('livechat', [LivechatController::class, 'Umumindex'])->name('umum.livechat');
    Route::post('livechat/start', [LivechatController::class, 'UmumstartSession'])->name('umum.livechatstart');
    Route::post('/livechat/send', [LivechatController::class, 'UmumsendMessageAjax'])->name('umumlivechat.send');


    // Tambahkan route lain untuk Umum di sini
});
