Route::get('/upload', [App\Http\Controllers\ImageController::class, 'index']);
Route::post('/uploadx', [App\Http\Controllers\ImageController::class, 'upload'])->name('upload.image');
Route::post('/update', [App\Http\Controllers\ImageController::class, 'update'])->name('update');

Route::get('/readFiles', [App\Http\Controllers\ImageController::class, 'readFiles'])->name('readFiles');

Route::post('/delete-file', [App\Http\Controllers\ImageController::class, 'deleteFile'])->name('deleteFile');