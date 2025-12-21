# Force Password Change on First Login - Feature

This feature forces users to change their password on their first login for enhanced security.

## How It Works

### 1. **First Login Flow**
- User logs in with default credentials (admin/admin123)
- Session flag `first_login` is set to `true`
- User is redirected to `change-password.php` page
- User cannot access the dashboard until password is changed

### 2. **Password Change Page** (`change-password.php`)
- **Requires**: Current password verification
- **New Password**: Minimum 8 characters
- **Confirmation**: New password must match confirmation
- **Real-time validation**: Shows password requirements
- **Status indicators**: Visual feedback for password strength
- **Logout option**: Users can logout without changing password

### 3. **Password Update Process**
- Current password is verified against config
- New password is saved to `.env` file
- Session flag `first_login` is cleared
- User is redirected to dashboard

### 4. **Settings Page** (`settings.php`)
- Users can change password anytime after first login
- Accessible from dashboard via Settings button
- Same validation rules apply
- Dashboard link to quickly return

## Files Modified/Created

### New Files:
1. **change-password.php** - First login password change page
2. **settings.php** - Account settings page for password management
3. **logout.php** - Secure logout handler

### Modified Files:
1. **session.php** - Added `updatePassword()` method
2. **login.php** - Redirects to change-password.php on first login
3. **dashboard.php** - Added Settings button in navbar

## Security Features

✅ **Password Requirements**:
- Minimum 8 characters
- Must match confirmation field
- Real-time validation feedback

✅ **Secure Storage**:
- Passwords stored in .env file
- Only accessible from PHP backend
- Current password verification required

✅ **Session Management**:
- First login flag prevents dashboard access
- Logout clears all sessions
- Automatic session timeout

✅ **User Feedback**:
- Success/error messages
- Real-time password validation
- Visual requirement indicators

## User Flow

```
1. User opens application
   ↓
2. Login page (login.php)
   ↓
3. Enter credentials (admin / admin123)
   ↓
4. First login detected
   ↓
5. Redirect to Change Password (change-password.php)
   ↓
6. Enter current password verification
   ↓
7. Enter new password (min 8 chars)
   ↓
8. Confirm new password
   ↓
9. Submit and verify
   ↓
10. Password updated in .env
   ↓
11. Session updated
   ↓
12. Redirect to Dashboard (dashboard.php)
   ↓
13. Access Settings anytime via ⚙️ button
```

## Admin Password Change

### For Administrators:
- Change password in `.env` file directly: `ADMIN_PASSWORD=newpassword`
- OR use the Settings page (⚙️ Settings button in navbar)
- Both methods work identically

### Resetting to Default:
- Edit `.env` file
- Set `ADMIN_PASSWORD=admin123`
- Restart application

## Best Practices

1. **On First Deployment**:
   - Users are forced to change default password
   - Choose a strong, unique password
   - Do not share credentials

2. **Ongoing Security**:
   - Users can change password anytime via Settings
   - Change password periodically (e.g., quarterly)
   - Use strong passwords (8+ characters, mix case if possible)

3. **Admin Management**:
   - Keep default credentials secure during setup
   - Change admin password immediately after deployment
   - Document new credentials securely

## Configuration

No additional configuration needed. The feature works automatically with:
- Existing `.env` file
- PHP sessions
- Current authentication system

## Troubleshooting

### "Failed to update password" error
- Check `.env` file permissions (should be writable)
- Verify PHP has write access to project directory
- In Docker, this is automatically handled

### Can't login after password change
- Verify new password is correct in `.env` file
- Check for typos
- Restart application/container

### Session issues
- Clear browser cookies
- Check PHP session storage permissions
- Restart application

## Technical Details

### Password Update Method
```php
Session::updatePassword($newPassword)
```
- Reads current `.env` file
- Finds `ADMIN_PASSWORD=` line
- Updates with new password
- Writes back to file
- Returns success/failure status

### Session Flags
- `authenticated`: User is logged in
- `first_login`: User must change password
- `password_changed`: Password has been changed

## Future Enhancements (Optional)

- Password history (prevent reusing old passwords)
- Password expiration policy
- Multiple user accounts with different roles
- Password complexity requirements (uppercase, numbers, symbols)
- Email notifications for password changes
- Two-factor authentication (2FA)
- Password reset via email
