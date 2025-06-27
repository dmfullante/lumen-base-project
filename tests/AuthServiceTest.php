<?php

namespace Tests;

use App\Models\User;
use App\Notifications\UserNotification;
use \Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AuthServiceTest extends TestCase
{
    public function loginPinInputErrorProvider(): array
    {
        return [
            'pin below 6 digit' => [123, 422, 'errors'],
            'pin above 6 digit' => [1234566, 422, 'errors'],
            'pin is null' => [null, 422, 'errors'],
            'pin is empty string' => ['', 422, 'errors']
        ];
    }

    public function otpRequestInputErrorProvider(): array
    {
        return [
            'otp request invalid email format' => [['key' => 'email', 'value' => 'testasds.com'], 422, 'errors'],
            'otp request invalid contact format' => [['key' => 'contact_no', 'value' => '3242123'], 422, 'errors'],
            'otp request invalid email' => [['key' => 'email', 'value' => 'admin@example.com'], 422, 'errors'],
            'otp request invalid contact' => [['key' => 'contact_no', 'value' => '09123123123'], 422, 'errors'],
        ];
    }
    
    public function testLoginValidPinCredential()
    {
        $request = new Request();
        $request->replace(['pin' => '123456']);

        // Act: Call the real login method
        $authService = new AuthService();
        $response = $authService->login($request);

        // Assert: Response is correct
        $this->assertTrue($response['status']);
        $this->assertSame(200, $response['code']);
        $this->assertSame('test', $response['result']['user']['name']);
        $this->assertArrayHasKey('token', $response['result']);
    }

    public function testLoginInvalidPinCredential()
    {
        $request = new Request();
        $request->replace(['pin' => '123451']);

        // Act: Call the real login method
        $authService = new AuthService();
        $response = $authService->login($request);

        // Assert: Response is correct
        $this->assertFalse($response['status']);
        $this->assertSame(401, $response['code']);
    }

    /**
     * @dataProvider loginPinInputErrorProvider
     */
    public function testLoginPinInputError($pin, $expected_status, $expected_key)
    {
        $request = new Request();
        $request->replace(['pin' => $pin]);

         // Act: Call the real login method
        $authService = new AuthService();
        $response = $authService->login($request);

        // Assert: Response is correct
        $this->assertSame($expected_status, $response['code']);
        $this->assertArrayHasKey($expected_key, $response['result']);
    }

    public function testOtpRequest()
    {
        // Fake all notifications
        Notification::fake();

        // Create a user
        $user = User::where('contact_no', '09123456789')->first();

        // Create request with contact_no
        $request = new Request([
            'contact_no' => $user->contact_no,
        ]);

        // Call the AuthService
        $service = new AuthService();
        $response = $service->otpRequest($request);

        // Assert success
        $this->assertTrue($response['status']);
        $this->assertSame(200, $response['code']);
        $this->assertNotNull($response['result']['user']->otp);

        // Assert that UserNotification was sent to this user
        Notification::assertSentTo(
            [$user],
            UserNotification::class,
            function ($notification) use ($response) {
                return $response['result']['user']->otp == $notification->data['otp'];
            }
        );
    }

    /**
     * @dataProvider otpRequestInputErrorProvider
     */
    public function testOtpRequestInputInputError($input, $expected_status, $expected_key)
    {
        // Create request with contact_no
        $request = new Request([
            $input['key'] => $input['value'],
        ]);

        // Call the AuthService
        $service = new AuthService();
        $response = $service->otpRequest($request);

        // Assert success
        $this->assertFalse($response['status']);
        $this->assertSame($expected_status, $response['code']);
        $this->assertArrayHasKey($expected_key, $response['result']);
    }
}
