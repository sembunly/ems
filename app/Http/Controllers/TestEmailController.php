<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Mail\OrderInvoiceMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class TestEmailController extends Controller
{
    /**
     * Send a test email using SendGrid SDK directly.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendTestEmail(Request $request): JsonResponse
    {
        // Validate request parameters
        $request->validate([
            'email' => 'required|email',
            'message' => 'required|string|max:1000',
        ]);

        $toEmail = $request->input('email');
        $message = $request->input('message');

        try {
            // Create the email using SendGrid Mail class
            $email = new \SendGrid\Mail\Mail();
            $email->setFrom("khomkhit.biu@gmail.com", "Laptop Store Test");
            $email->setSubject("Test Email from Laptop Store");
            $email->addTo($toEmail, "Test Recipient");
            $email->addContent("text/plain", $message);
            $email->addContent(
                "text/html", 
                "<strong>Laptop Store Test Email</strong><br><br>" . nl2br(htmlspecialchars($message))
            );

            // Initialize SendGrid with API key from environment
            $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));

            // Send the email
            $response = $sendgrid->send($email);

            // Return response based on status code
            $statusCode = $response->statusCode();
            
            if ($statusCode >= 200 && $statusCode < 300) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test email sent successfully!',
                    'status_code' => $statusCode,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send email',
                    'status_code' => $statusCode,
                    'response' => $response->body(),
                ], $statusCode);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending email: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display test email form page.
     *
     * @return \Illuminate\View\View
     */
    public function showTestForm()
    {
        return view('test-email');
    }

    /**
     * Get the CSRF token for testing email endpoint in Postman.
     *
     * @return JsonResponse
     */
    public function getCsrfToken(): JsonResponse
    {
        return response()->json([
            'csrf_token' => csrf_token(),
        ]);
    }

    /**
     * Test the OrderInvoiceMail by sending it to the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function testInvoiceEmail(Request $request): JsonResponse
    {
        // Validate request parameters
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'User must be authenticated to test invoice email',
            ], 401);
        }

        $orderId = $request->input('order_id');
        $user = auth()->user();

        try {
            // Find the order with its items
            $order = Order::with('orderItems.product')->findOrFail($orderId);

            // Send the OrderInvoiceMail to the authenticated user's email
            Mail::to($user->email)->send(new OrderInvoiceMail($order));

            return response()->json([
                'success' => true,
                'message' => 'Invoice email sent successfully!',
                'data' => [
                    'order_id' => $order->id,
                    'order_total' => $order->total_amount,
                    'recipient_email' => $user->email,
                    'recipient_name' => $user->name ?? $user->email,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send invoice email: ' . $e->getMessage(),
            ], 500);
        }
    }
}
