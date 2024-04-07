        
        function encryptVigenere(inputString) {
            const key = document.getElementById('cipherKey').value.toUpperCase().replace(/[^A-Za-z]/g, '');
            let encryptedText = '';

            // Iterate over each character in the input string
            for (let i = 0, j = 0; i < inputString.length; i++) {
                const currentChar = inputString[i];
                let baseCharCode = currentChar >= 'a' && currentChar <= 'z' ? 'a'.charCodeAt(0) : 'A'.charCodeAt(0);

                if ((currentChar >= 'A' && currentChar <= 'Z') || (currentChar >= 'a' && currentChar <= 'z')) {
                    // Encrypt alphabetic characters
                    const encryptedChar = String.fromCharCode(
                        ((currentChar.charCodeAt(0) - baseCharCode) +
                        (key[j % key.length].charCodeAt(0) - 'A'.charCodeAt(0))) % 26 +
                        baseCharCode
                    );
                    encryptedText += encryptedChar;
                    j++; // Move to the next letter in the key
                } else {
                    // Non-alphabetic characters are added directly
                    encryptedText += currentChar;
                }
            }
            return encryptedText;
        }

        function getFixedSaltFromString(str) {
            // var newstr = encryptVigenere(str);
            return new TextEncoder().encode(str);
        }
        async function deriveKeyFromPassphrase(passphrase) {
            try {
                const salt = getFixedSaltFromString(passphrase);

                const passphraseBytes = new TextEncoder().encode(passphrase);

                const keyMaterial = await window.crypto.subtle.importKey(
                    "raw",
                    passphraseBytes,
                    { name: "PBKDF2" },
                    false,
                    ["deriveKey"]
                );

                return await window.crypto.subtle.deriveKey(
                    {
                        name: "PBKDF2",
                        salt: salt,
                        iterations: 100000,
                        hash: "SHA-256"
                    },
                    keyMaterial,
                    { name: "AES-GCM", length: 256 },
                    true, // Set to false if the key should not be extractable
                    ["encrypt", "decrypt"]
                );
            } catch (e) {
                console.error('Error deriving key:', e);
                throw e; // Re-throw the error to be handled by the caller
            }
        }
        function decryptVigenere(encryptedText) {
            const key = document.getElementById('cipherKey').value.toUpperCase().replace(/[^A-Za-z]/g, '');
            let decryptedText = '';

            for (let i = 0, j = 0; i < encryptedText.length; i++) {
                const currentChar = encryptedText[i];
                let baseCharCode = currentChar >= 'a' && currentChar <= 'z' ? 'a'.charCodeAt(0) : 'A'.charCodeAt(0);

                if ((currentChar >= 'A' && currentChar <= 'Z') || (currentChar >= 'a' && currentChar <= 'z')) {
                    // Decrypt alphabetic characters
                    const decryptedChar = String.fromCharCode(
                        ((currentChar.charCodeAt(0) - baseCharCode) -
                        (key[j % key.length].charCodeAt(0) - 'A'.charCodeAt(0)) + 26) % 26 +
                        baseCharCode
                    );
                    decryptedText += decryptedChar;
                    j++; // Move to the next letter in the key
                } else {
                    // Non-alphabetic characters are added directly
                    decryptedText += currentChar;
                }
            }
            return decryptedText;
        }
        async function encryptMessageToBase64() {
            const inputString = encryptVigenere(document.getElementById('userInput').value);
            const password = deriveKeyFromPassphrase(document.getElementById('cipherKey').value);
            const encoder = new TextEncoder();

            // Generate a random IV and encode the password
            const iv = crypto.getRandomValues(new Uint8Array(12));
            const keyMaterial = encoder.encode(password);
            const key = await crypto.subtle.importKey("raw", keyMaterial, "AES-GCM", false, ["encrypt"]);

            const encrypted = await crypto.subtle.encrypt({ name: "AES-GCM", iv }, key, encoder.encode(inputString));
            const ivAndEncryptedData = new Uint8Array(iv.length + encrypted.byteLength);
            ivAndEncryptedData.set(iv, 0);
            ivAndEncryptedData.set(new Uint8Array(encrypted), iv.length);

            // Convert the IV+encrypted data array to a Base64 string
            const base64Encrypted = btoa(String.fromCharCode(...ivAndEncryptedData));
            document.getElementById('output1').textContent = base64Encrypted;
        }

        async function decryptMessageFromBase64() {
            const base64Encrypted = document.getElementById('userOutput').value;
            const passphrase = document.getElementById('outputKey').value;
            const decoder = new TextDecoder();

            try {
                const ivAndEncryptedData = Uint8Array.from(atob(base64Encrypted), c => c.charCodeAt(0));
                const iv = ivAndEncryptedData.slice(0, 12);
                const encryptedData = ivAndEncryptedData.slice(12);
                const encoder = new TextEncoder();
                // Correctly await the derived key before attempting to decrypt
                const password =  deriveKeyFromPassphrase(passphrase);
                const keyMaterial = encoder.encode(password);
                const key = await crypto.subtle.importKey("raw", keyMaterial, "AES-GCM", false, ["decrypt"]);
                const decrypted = await crypto.subtle.decrypt({ name: "AES-GCM", iv }, key, encryptedData);

                var resultMessage=decryptVigenere( decoder.decode(decrypted),passphrase);
                document.getElementById('output').textContent = resultMessage
            } catch (e) {
                console.error('Decryption failed', e);
                document.getElementById('output').textContent = 'Decryption failed';
            }
        }


