# Easy and secure Encryption

To install simply run:

```
composer install
```

Using it by replacing {} either like this

```
php index.php security:encrypt ./{stuff_to_encrypt} ./{encryption_image.jpeg} ./{path_encrypted_file}
```

or like this

```
php index.php security:decrypt ./{encrypted_file} ./{encryption_image.jpeg} ./{decrypted_stuff_goes_there}/
```

Software is licensed under MIT, see LICENSE file.
