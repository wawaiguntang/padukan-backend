<?php

namespace Modules\Authentication\Tests\Unit\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Authentication\Database\Factories\UserFactory;
use Modules\Authentication\Enums\UserStatus;
use Modules\Authentication\Models\User;
use Modules\Authentication\Repositories\User\UserRepository;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

/**
 * User Repository Test
 *
 * This test class covers all UserRepository functionality
 * including CRUD operations and user management features.
 */
class UserRepositoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The UserRepository instance
     *
     * @var UserRepository
     */
    protected UserRepository $repository;

    /**
     * Set up the test environment
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(\Modules\Authentication\Repositories\User\IUserRepository::class);
    }

    /**
     * Generate unique phone number for testing
     *
     * @return string
     */
    private function generateUniquePhone(): string
    {
        return $this->faker->numerify('+628##########');
    }

    /**
     * Generate unique email for testing
     *
     * @return string
     */
    private function generateUniqueEmail(): string
    {
        return $this->faker->unique()->email();
    }

    /**
     * Test finding user by identifier (phone)
     *
     * @return void
     */
    public function test_find_by_identifier_with_phone()
    {
        // Arrange
        $phone = $this->faker->numerify('+628##########');
        $user = UserFactory::new()->create(['phone' => $phone, 'email' => null]);

        // Act
        $foundUser = $this->repository->findByIdentifier($phone);

        // Assert
        $this->assertNotNull($foundUser);
        $this->assertEquals($user->id, $foundUser->id);
        $this->assertEquals($phone, $foundUser->phone);
    }

    /**
     * Test finding user by identifier (email)
     *
     * @return void
     */
    public function test_find_by_identifier_with_email()
    {
        // Arrange
        $email = $this->faker->unique()->email();
        $user = UserFactory::new()->create(['phone' => null, 'email' => $email]);

        // Act
        $foundUser = $this->repository->findByIdentifier($email);

        // Assert
        $this->assertNotNull($foundUser);
        $this->assertEquals($user->id, $foundUser->id);
        $this->assertEquals($email, $foundUser->email);
    }

    /**
     * Test finding user by identifier returns null when not found
     *
     * @return void
     */
    public function test_find_by_identifier_returns_null_when_not_found()
    {
        // Act
        $foundUser = $this->repository->findByIdentifier('nonexistent@example.com');

        // Assert
        $this->assertNull($foundUser);
    }

    /**
     * Test finding user by ID
     *
     * @return void
     */
    public function test_find_by_id()
    {
        // Arrange
        $user = UserFactory::new()->create();

        // Act
        $foundUser = $this->repository->findById($user->id);

        // Assert
        $this->assertNotNull($foundUser);
        $this->assertEquals($user->id, $foundUser->id);
    }

    /**
     * Test finding user by ID returns null when not found
     *
     * @return void
     */
    public function test_find_by_id_returns_null_when_not_found()
    {
        // Act
        $foundUser = $this->repository->findById(Uuid::uuid4()->toString());

        // Assert
        $this->assertNull($foundUser);
    }

    /**
     * Test creating a new user
     *
     * @return void
     */
    public function test_create_user()
    {
        // Arrange
        $phone = $this->generateUniquePhone();
        $email = $this->generateUniqueEmail();
        $userData = [
            'phone' => $phone,
            'email' => $email,
            'password' => 'hashedpassword',
            'status' => UserStatus::ACTIVE,
        ];

        // Act
        $user = $this->repository->create($userData);

        // Assert
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($phone, $user->phone);
        $this->assertEquals($email, $user->email);
        $this->assertNotEquals('hashedpassword', $user->password); // Password should be hashed
        $this->assertEquals(UserStatus::ACTIVE, $user->status);
        $this->assertNotNull($user->id);
    }

    /**
     * Test updating an existing user
     *
     * @return void
     */
    public function test_update_user()
    {
        // Arrange
        $user = User::create([
            'phone' => $this->generateUniquePhone(),
            'email' => $this->generateUniqueEmail(),
            'password' => 'password',
            'status' => \Modules\Authentication\Enums\UserStatus::ACTIVE,
        ]);

        $updateData = [
            'phone' => $this->generateUniquePhone(),
            'email' => $this->generateUniqueEmail(),
        ];

        // Act
        $result = $this->repository->update($user->id, $updateData);

        // Assert
        $this->assertTrue($result);
        $user->refresh();
        $this->assertEquals($updateData['phone'], $user->phone);
        $this->assertEquals($updateData['email'], $user->email);
    }

    /**
     * Test updating non-existent user returns false
     *
     * @return void
     */
    public function test_update_non_existent_user_returns_false()
    {
        // Act
        $result = $this->repository->update(Uuid::uuid4()->toString(), ['phone' => '+6281234567890']);

        // Assert
        $this->assertFalse($result);
    }

    /**
     * Test deleting a user
     *
     * @return void
     */
    public function test_delete_user()
    {
        // Arrange
        $user = User::create([
            'phone' => $this->generateUniquePhone(),
            'email' => $this->generateUniqueEmail(),
            'password' => 'password',
            'status' => \Modules\Authentication\Enums\UserStatus::ACTIVE,
        ]);

        // Act
        $result = $this->repository->delete($user->id);

        // Assert
        $this->assertTrue($result);
        $this->assertNull($this->repository->findById($user->id));
    }

    /**
     * Test deleting non-existent user returns false
     *
     * @return void
     */
    public function test_delete_non_existent_user_returns_false()
    {
        // Act
        $result = $this->repository->delete(Uuid::uuid4()->toString());

        // Assert
        $this->assertFalse($result);
    }

    /**
     * Test checking if user exists by identifier
     *
     * @return void
     */
    public function test_exists_by_identifier()
    {
        // Arrange
        $phone = $this->generateUniquePhone();
        $email = $this->generateUniqueEmail();
        User::create([
            'phone' => $phone,
            'email' => $email,
            'password' => 'password',
            'status' => \Modules\Authentication\Enums\UserStatus::ACTIVE,
        ]);

        // Act & Assert
        $this->assertTrue($this->repository->existsByIdentifier($phone));
        $this->assertFalse($this->repository->existsByIdentifier('nonexistent@example.com'));
    }

    /**
     * Test updating user status
     *
     * @return void
     */
    public function test_update_status()
    {
        // Arrange
        $user = User::create([
            'phone' => $this->generateUniquePhone(),
            'email' => $this->generateUniqueEmail(),
            'password' => 'password',
            'status' => UserStatus::PENDING,
        ]);

        // Act
        $result = $this->repository->updateStatus($user->id, UserStatus::ACTIVE);

        // Assert
        $this->assertTrue($result);
        $user->refresh();
        $this->assertEquals(UserStatus::ACTIVE, $user->status);
    }

    /**
     * Test updating status for non-existent user returns false
     *
     * @return void
     */
    public function test_update_status_non_existent_user_returns_false()
    {
        // Act
        $result = $this->repository->updateStatus(Uuid::uuid4()->toString(), UserStatus::ACTIVE);

        // Assert
        $this->assertFalse($result);
    }
}