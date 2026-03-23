# Quick Setup Guide

## 🚀 Start the Application in 2 Minutes

### Step 1: Start PHP Backend
```bash
cd backend
php -S localhost:8000
```

2. **Open Frontend**
- Double-click `frontend/index.html` to open in browser
- OR open `http://localhost/your-project-folder/frontend/index.html`

### Step 3: Use the App
- Upload an image
- Enter a secret message
- Download your stego image!

## 📁 Project Files (Only 7 files total)

**Frontend (3 files):**
- `frontend/index.html` - Main page
- `frontend/style.css` - Beautiful styling
- `frontend/script.js` - Interactive functionality

**Backend (4 files):**
- `backend/api/embed.php` - Hide messages
- `backend/api/extract.php` - Extract messages  
- `backend/includes/Steganography.php` - LSB algorithm
- `backend/includes/Encryption.php` - AES encryption

## ✅ What Works

- Hide secret messages in images
- Extract hidden messages
- Optional encryption with passwords
- Support for PNG, JPEG, BMP
- Modern, responsive interface
- Real-time image preview

## 🔧 Requirements

- PHP 7.4+ with GD extension
- Modern web browser
- That's it! No Node.js, no npm, no complex setup

## 🧪 Test It

1. Start the backend server
2. Open index.html in browser
3. Try hiding "Hello World" in any image
4. Download and extract it back

Perfect for 2nd phase evaluation - simple, functional, and impressive!
