SecureVault Class Documentation
==============================

The SecureVault class provides functionality for encrypting data, generating digital signatures, and working with encrypted files. It utilizes the sodium_crypto_secretbox encryption algorithm and the Firebase\JWT\JWT library for digital signature generation and verification.

Class Methods:
--------------

1. cipher(string $value, string $key): string
   - Encrypts the given string using the provided key.
   - Parameters:
     - $value (string): The string to be encrypted.
     - $key (string): The encryption key.
   - Returns:
     - Encrypted string.

2. deCipher(string $cipher, string $key): mixed
   - Decrypts the encrypted string and returns the original plaintext string.
   - Parameters:
     - $cipher (string): The encrypted string to be decrypted.
     - $key (string): The decryption key.
   - Returns:
     - The decrypted plaintext string or false if decryption fails.

3. `retrieveJWTFromFile(string $data): string|bool`
   - Retrieves the JWT (JSON Web Token) from the given file content.
   - Parameters:
     - $data (string): The content of the encrypted file.
   - Returns:
     - The JWT if found in the content, or false if not found.

4. fileDecipher(string $file, string $key): array|bool
   - Decrypts the content of an encrypted file and returns the deciphered data as an array.
   - Parameters:
     - $file (string): The path to the encrypted file.
     - $key (string): The decryption key.
   - Throws:
     - Exception if the file does not exist, has an invalid name format, or contains invalid JSON content.
   - Returns:
     - The deciphered data as an array if decryption and verification are successful, or false otherwise.

5. verifySignature(string $fileContent, string $jwt, string $secreteKey): bool
   - Verifies the digital signature of the file content using the provided JWT and secret key.
   - Parameters:
     - $fileContent (string): The content of the encrypted file.
     - $jwt (string): The JSON Web Token (JWT) to be verified.
     - $secreteKey (string): The secret key used for verification.
   - Returns:
     - True if the signature is valid, false otherwise.

6. generateSignature(string $data, string $key, array $extra = []): string
   - Generates a digital signature (JWT) for the given data using the provided key.
   - Parameters:
     - $data (string): The data to be signed.
     - $key (string): The key used for signing.
     - $extra (array): Additional data to include in the signature payload. (Optional)
   - Returns:
     - The generated digital signature (JWT) as a string.

7. encryptToFile(array $fileContent, string $key, string $dir = __DIR__, string $prefix = 'config_'): bool|int
   - Encrypts the provided file content, generates a digital signature, and writes it to a file.
   - Parameters:
     - $fileContent (array): The data to be encrypted and written to the file.
     - $key (string): The encryption key.
     - $dir (string): The directory where the encrypted file should be stored. (Optional)
     - $prefix (string): The prefix to be added to the file name. (Optional)
   - Throws:
     - Exception if the given directory is not a valid directory.
     - Exception if encryption or signature generation fails.
   - Returns:
     - The number of bytes written to the file on success, or false if an error occurs.

Additional Notes:
-----------------

- The SecureVault class provides functionality for secure encryption, decryption, and file operations.
- It uses the sodium_crypto_secretbox algorithm for encryption, which is considered secure.
- Digital signatures (JWTs) are generated and verified using the HS256 algorithm from the Firebase\JWT\JWT library.
- Proper key management and secure storage of sensitive information (such as encryption keys and secret keys) are essential for maintaining the security of the encrypted data.
- Input validation and sanitization should be performed to prevent security vulnerabilities.
- It's recommended to follow secure coding practices and conduct security assessments to ensure the overall security of the application.

For more information, please refer to the class documentation and code comments.

