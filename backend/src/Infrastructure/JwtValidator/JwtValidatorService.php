<?php

namespace App\Infrastructure\JwtValidator;

use App\Application\JwtValidator\JwtValidatorInterface;
use App\Domain\Entity\User;
use App\Domain\Exception\JwtValidationException;
use App\Domain\Repository\UserRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class JwtValidatorService implements JwtValidatorInterface
{
    public function __construct(
        private JWTEncoderInterface $jwtEncoder,
        private UserRepositoryInterface $userRepository
    ) {}


    public function validate(string $token): User
    {
        try {
            // Decodifica y valida la firma automáticamente
            $payload = $this->jwtEncoder->decode($token);

            if (!$payload) {
                throw new JwtValidationException("Token could not be read!");
            }

            // Lexik por defecto guarda el user en "username" o "sub"
            $userId = (string) $payload['username'];

            if (!$userId) {
                throw new JwtValidationException('Payload does not contain valid user!');
            }

            $user = $this->userRepository->findByEmail($userId);

            if (!$user instanceof User) {
                throw new JwtValidationException('User not found with given email!');
            }

            return $user;
            
        } catch (\Exception) {
            throw new JwtValidationException();
        }
    }
}
