#!/usr/bin/env python3
"""
Script untuk generate bcrypt password hash
Gunakan untuk update password di database
"""

import subprocess
import sys

def hash_password(password):
    """Generate bcrypt hash dari password"""
    try:
        # Coba pake htpasswd (standard di Linux/Mac)
        result = subprocess.run(
            ['htpasswd', '-nbBC', '10', 'dummy', password],
            capture_output=True,
            text=True
        )
        if result.returncode == 0:
            hash_value = result.stdout.split(':')[1].strip()
            return hash_value
    except FileNotFoundError:
        pass
    
    # Fallback: pake PHP jika tersedia
    try:
        php_code = f"echo password_hash('{password}', PASSWORD_BCRYPT);"
        result = subprocess.run(
            ['php', '-r', php_code],
            capture_output=True,
            text=True
        )
        if result.returncode == 0:
            return result.stdout.strip()
    except FileNotFoundError:
        pass
    
    return None

if __name__ == '__main__':
    password = input("Masukkan password yang ingin di-hash: ")
    
    hash_result = hash_password(password)
    
    if hash_result:
        print(f"\n✅ Hash berhasil:")
        print(f"Password: {password}")
        print(f"Hash: {hash_result}")
        print(f"\n💾 Update query untuk phpMyAdmin:")
        print(f"UPDATE users SET password = '{hash_result}' WHERE username = 'demo';")
    else:
        print("❌ Error: Tidak bisa generate hash. Pastikan PHP terinstall.")
        sys.exit(1)
