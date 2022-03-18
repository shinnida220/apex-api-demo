<?php
namespace App\Traits;

trait ApiResponse{

  protected function successResponse($message, $data = null, $code = 200) {
		return response()->json([
			'status' => true, 
			'message' => $message, 
			'data' => $data
		], $code);
	}

	protected function errorResponse($message = 'Operation failed.', $data = null, $code = 200) {
		return response()->json([
			'status' => false,
			'message' => $message,
			'data' => $data
		], $code);
	}
}