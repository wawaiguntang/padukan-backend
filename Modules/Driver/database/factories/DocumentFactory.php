<?php

namespace Modules\Driver\Database\Factories;

use Modules\Driver\Models\Document;
use Modules\Driver\Models\Profile;
use Modules\Driver\Enums\DocumentTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Driver\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Document::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'documentable_id' => Profile::factory(),
            'documentable_type' => Profile::class,
            'type' => $this->faker->randomElement([
                DocumentTypeEnum::ID_CARD,
                DocumentTypeEnum::SELFIE_WITH_KTP,
                DocumentTypeEnum::SIM,
                DocumentTypeEnum::STNK,
                DocumentTypeEnum::VEHICLE_PHOTO
            ]),
            'file_path' => 'documents/' . Str::uuid() . '.jpg',
            'file_name' => $this->faker->word() . '.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => $this->faker->numberBetween(100000, 2000000),
            'meta' => [],
            'expiry_date' => $this->faker->optional(0.7)->dateTimeBetween('now', '+2 years'),
            'is_verified' => false,
            'verification_status' => 'pending',
            'verified_at' => null,
            'verified_by' => null,
        ];
    }

    /**
     * Indicate that the document is verified.
     */
    public function verified(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_verified' => true,
            'verification_status' => 'approved',
            'verified_at' => now(),
            'verified_by' => Str::uuid(),
        ]);
    }

    /**
     * Create an ID card document.
     */
    public function idCard(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => DocumentTypeEnum::ID_CARD,
            'meta' => ['nik' => $this->faker->numerify('################')],
        ]);
    }

    /**
     * Create a selfie with KTP document.
     */
    public function selfieWithKtp(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => DocumentTypeEnum::SELFIE_WITH_KTP,
            'meta' => ['location' => $this->faker->city()],
        ]);
    }

    /**
     * Create a STNK document.
     */
    public function stnk(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => DocumentTypeEnum::STNK,
            'meta' => ['vehicle_number' => strtoupper($this->faker->bothify('?? #### ??'))],
        ]);
    }
}
