# Image Steganography Web Application

A simple, secure web application for hiding and extracting secret messages within images using LSB (Least Significant Bit) steganography technique combined with encryption.

## Features

- **Hide Messages**: Embed secret messages into images using LSB technique
- **Extract Messages**: Retrieve hidden messages from stego images
- **Encryption**: Optional AES encryption for additional security
- **Simple Interface**: Clean HTML/CSS/JavaScript frontend
- **Multiple Image Formats**: Support for PNG, BMP, and JPEG images

## Project Structure (Simplified)

```
├── frontend/           # Frontend files
│   ├── index.html      # Main HTML file
│   ├── style.css       # Styling
│   └── script.js       # Frontend JavaScript
├── backend/            # PHP backend
│   ├── api/
│   │   ├── embed.php   # Embed message API
│   │   └── extract.php # Extract message API
│   ├── includes/
│   │   ├── Steganography.php # LSB algorithm
│   │   └── Encryption.php     # AES encryption
│   └── uploads/        # Temporary file storage
└── README.md          # This file
```

## Technology Stack

### Frontend
- HTML5
- CSS3 (with modern gradients and animations)
- Vanilla JavaScript
- No frameworks - pure web technologies

### Backend
- PHP
- Image Processing (GD Library)
- AES Encryption
- File Upload Handling

## How It Works

1. **Embedding Process**:
   - User uploads an image and enters a secret message
   - Optional encryption key can be provided
   - Message is encrypted (if key provided)
   - LSB algorithm embeds the message into image pixels
   - Stego image is generated for download

2. **Extraction Process**:
   - User uploads a stego image
   - Same encryption key (if used during embedding)
   - LSB algorithm extracts hidden message
   - Message is decrypted (if encrypted)
   - Original message is displayed

## Installation & Setup

### Prerequisites
- PHP 7.4+ with GD extension
- Web server (Apache/Nginx or PHP built-in server)

### Quick Start

1. **Start PHP Backend**:
   ```bash
   cd backend
   php -S localhost:8000
   ```

2. **Open Frontend**:
   - Open `index.html` directly in your browser
   - Or serve it with any web server

3. **Access the Application**:
   - Frontend: Open index.html in browser
   - Backend API: http://localhost:8000

## Usage

1. **Hide a Message**:
   - Click "Hide Message" tab
   - Upload an image
   - Enter your secret message
   - Optionally add an encryption key
   - Click "Hide Message"
   - Download the stego image

2. **Extract a Message**:
   - Click "Extract Message" tab
   - Upload a stego image
   - Enter the encryption key (if used)
   - Click "Extract Message"
   - View the hidden message

## Security Features

- AES-256 encryption for message protection
- Secure file upload handling
- Input validation and sanitization
- Temporary file cleanup

## Supported Image Formats

- PNG (recommended for lossless quality)
- BMP
- JPEG (with some quality loss possible)

## File Count & Simplicity

This project is intentionally kept simple with minimal files:
- **3 frontend files** (HTML, CSS, JS)
- **4 backend files** (2 API endpoints, 2 core classes)
- **Total: ~7 files** for a complete working application

This represents a human approach to building - simple, functional, and easy to understand.
