<?php

namespace Tests\Unit\Services;

use App\Helpers\Helper;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\ProfileInterface;
use App\Interfaces\ProfileUserGroupInterface;
use App\Interfaces\UserInterface;
use App\Models\Profile;
use App\Models\User;
use App\Services\ManageAccountService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Helpers\TestDoubles;

class ManageAccountServiceTest extends TestCase
{
    use RefreshDatabase, TestDoubles;

    /** @var \Mockery\MockInterface&BaseFetchInterface */
    protected BaseFetchInterface $fetch;

    /** @var \Mockery\MockInterface&UserInterface */
    protected UserInterface $user;

    /** @var \Mockery\MockInterface&ProfileInterface */
    protected ProfileInterface $profile;

    /** @var \Mockery\MockInterface&ProfileUserGroupInterface */
    protected ProfileUserGroupInterface $profileUserGroup;

    /** @var \Mockery\MockInterface&CurrentUserInterface */
    protected CurrentUserInterface $currentUser;

    protected ManageAccountService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fetch = $this->mockBaseFetchInterface();
        $this->user = Mockery::mock(UserInterface::class);
        $this->profile = Mockery::mock(ProfileInterface::class);
        $this->profileUserGroup = Mockery::mock(ProfileUserGroupInterface::class);
        $this->currentUser = $this->mockCurrentUserInterface();

        $this->service = new ManageAccountService(
            $this->fetch,
            $this->user,
            $this->profile,
            $this->profileUserGroup,
            $this->currentUser
        );

        $this->beforeApplicationDestroyed(function () {
            Mockery::close();
        });
    }

    #[Test]
    /**
     * This test verifies successful registration of both a user and a profile.
     *
     * Notes:
     * - Uses a full mock of `Auth::user()` because the service calls it inside a `DB::transaction()`,
     *   which can cause Laravel’s `actingAs()` or `User::factory()` to behave inconsistently.
     * - `storeUser` and `storeProfile` are mocked to simulate successful database operations.
     * - The profile response is returned as an anonymous class extending `Model` to satisfy
     *   the `returnModel()` method's expected type (i.e., Eloquent model).
     */
    public function it_registers_a_user_and_profile_successfully()
    {
        // Full Auth::user() mock — most reliable inside DB::transaction
        $fakeUser = (object)[
            'profile' => (object)['id' => 99],
        ];
        Auth::shouldReceive('user')->andReturn($fakeUser)->byDefault();

        $this->currentUser->shouldReceive('getProfileId')
            ->once()
            ->andReturn(99);

        $request = [
            'username' => 'testuser',
            'email' => 'user@example.com',
            'avatar' => 'avatar.png',
            'first_name' => 'Test',
            'middle_name' => 'M',
            'last_name' => 'User',
            'nickname' => 'T',
            'type' => 'admin',
            'contact_numbers' => ['123456789'],
            'is_admin' => true,
            'is_first_login' => true,
            'password' => 'testuser',
        ];

        $this->user->shouldReceive('storeUser')
            ->once()
            ->andReturn([
                'success' => true,
                'lastId' => 1,
                'data' => (object)['id' => 1],
            ]);

        //This anonymous class extends Model and gives you a mock Eloquent object.
        $profileData = new class(['id' => 123, 'first_name' => 'Test']) extends Model {
            protected $guarded = [];
            public $timestamps = false;
        };

        $this->profile->shouldReceive('storeProfile')
            ->once()
            ->andReturn([
                'success' => true,
                'data' => $profileData,
            ]);

        $response = $this->service->register($request);

        $this->assertEquals(201, $response['code']);
        $this->assertEquals(Helper::SUCCESS, $response['status']);
        $this->assertEquals('Profile registration successfully!', $response['message']);
        $this->assertEquals($profileData, $response['data']);
    }


    #[Test]
    public function it_throws_error_if_user_creation_fails()
    {
        $this->actingAs(User::factory()->make([
            'profile' => (object)['id' => 99],
        ]));

        $this->currentUser->shouldReceive('getProfileId')
            ->once()
            ->andReturn(99);

        $request = [
            'username' => 'failuser',
            'email' => 'fail@example.com',
            'first_name' => 'Fail',
            'password' => 'failuser',
        ];

        // Simulate user creation failure with no data
        $this->user->shouldReceive('storeUser')
            ->once()
            ->andReturn([
                'status' => Helper::ERROR,
                'message' => 'User creation failed!',
            ]);


        // Make sure storeProfile is never called
        $this->profile->shouldNotReceive('storeProfile');

        $response = $this->service->register($request);

        $this->assertEquals(500, $response['code']);
        $this->assertEquals(Helper::ERROR, $response['status']);
        $this->assertStringContainsString('User creation failed', $response['message']);
    }

    #[Test]
    public function it_throws_error_if_profile_creation_fails()
    {
        $this->actingAs(User::factory()->make([
            'profile' => (object)['id' => 99],
        ]));

        $this->currentUser->shouldReceive('getProfileId')
            ->once()
            ->andReturn(99);

        $this->user->shouldReceive('storeUser')
            ->once()
            ->andReturn([
                'success' => true,
                'lastId' => 1,
                'data' => (object)['id' => 1],
            ]);

        $this->profile->shouldReceive('storeProfile')
            ->once()
            ->andReturn([
                'status' => Helper::ERROR,
                'message' => 'Profile creation failed!',
            ]);

        $response = $this->service->register([
            'username' => 'testuser',
            'email' => 'user@example.com',
        ]);

        $this->assertEquals(500, $response['code']);
        $this->assertEquals(Helper::ERROR, $response['status']);
        $this->assertStringContainsString('Profile creation failed', $response['message']);
    }

    #[Test]
    public function it_updates_user_profile_successfully_with_password_change()
    {
        $profileId = 123;

        // Mock Auth::user() to return a profile ID
        $currentUserUser = (object)['profile' => (object)['id' => 99]];
        Auth::shouldReceive('user')->andReturn($currentUserUser);

        $this->currentUser->shouldReceive('getProfileId')
            ->once()
            ->andReturn(99);

        // Request data for update
        $request = [
            'avatar' => 'avatar.png',
            'first_name' => 'Updated',
            'middle_name' => 'Middle',
            'last_name' => 'Name',
            'nickname' => 'Nick',
            'type' => 'employee',
            'contact_numbers' => ['123456789'],
            'username' => 'updateduser',
            'email' => 'updated@example.com',
            'password' => 'newpassword123', // this will be bcrypt-ed
            'user_group_id' => 5,
        ];

        // Create a fake profile and associated user
        $mockProfile = new class extends Model {
            public $id = 123;
            public $avatar = 'avatar.png';
            public $first_name = 'First';
            public $middle_name = 'M';
            public $last_name = 'Last';
            public $nickname = 'Nick';
            public $type = 'admin';
            public $contact_numbers = ['987654321'];
            public $user;

            public function __construct()
            {
                $this->user = new class {
                    public $id = 1;
                    public $username = 'olduser';
                    public $email = 'old@example.com';
                    public $password = 'oldhashedpw';
                };
            }
        };

        // Mock the query builder and firstOrFail() using Mockery
        $fakeQueryBuilder = \Mockery::mock(Builder::class);
        $fakeQueryBuilder
            ->shouldReceive('firstOrFail')
            ->once()
            ->andReturn($mockProfile);

        $this->fetch
            ->shouldReceive('showQuery')
            ->once()
            ->with(Profile::class, $profileId)
            ->andReturn($fakeQueryBuilder);

        // Mock profile update
        $this->profile->shouldReceive('updateProfile')
            ->once()
            ->with(Mockery::type('array'), $profileId)
            ->andReturn(['success' => true]);

        // Mock user update
        $this->user->shouldReceive('updateUser')
            ->once()
            ->with(Mockery::on(function ($data) {
                return !empty($data['password']); // Ensure password is processed
            }), 1)
            ->andReturn(['success' => true]);

        // Mock user group update
        $this->profileUserGroup->shouldReceive('updateProfileUserGroupWithProfileId')
            ->once()
            ->with(['profile_id' => 123, 'user_group_id' => 5], $profileId)
            ->andReturn(['success' => true]);

        // Run the method
        $response = $this->service->updateUserProfile($request, $profileId);

        $this->assertEquals(200, $response['code']);
        $this->assertEquals(Helper::SUCCESS, $response['status']);
        $this->assertEquals('Profile updated successfully!', $response['message']);
        $this->assertEquals(123, $response['data']->id);
    }
}
