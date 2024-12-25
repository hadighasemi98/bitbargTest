<?php

namespace Tests\Unit\Http\Requests\Auth;

use App\Http\Requests\Auth\SignUpRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SignUpRequestTest extends TestCase
{
    /**
     * Test the validation rules of the SignUpRequest.
     */
    public function test_validation_rules()
    {
        $request = new SignUpRequest();

        $rules = $request->rules();

        $this->assertEquals([
            'email' => 'required|email',
            'password' => 'required|string',
        ], $rules);
    }

    /**
     * Test the authorization of the SignUpRequest.
     */
    public function test_authorization()
    {
        $request = new SignUpRequest;

        $this->assertFalse($request->authorize());
    }

    /**
     * Test that the request validates correctly with valid data.
     */
    public function test_request_with_valid_data()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $request = new SignUpRequest;
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the request fails validation with invalid data.
     */
    public function test_request_with_invalid_data()
    {
        $data = [
            'email' => 'invalid-email',
            'password' => '',
        ];

        $request = new SignUpRequest;
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }
}
