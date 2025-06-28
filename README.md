# Laravel Real-time Chat with Reverb

A modern, real-time chat application built with Laravel and Laravel Reverb for WebSocket broadcasting.

## Features

- ✅ Real-time messaging with Laravel Reverb
- ✅ Message persistence in database
- ✅ Rate limiting to prevent spam
- ✅ Input validation and sanitization
- ✅ Error handling and user feedback
- ✅ Responsive UI with Tailwind CSS
- ✅ Connection status indicators
- ✅ Message history loading
- ✅ Clean, maintainable code architecture

## Architecture Overview

### Backend Components

#### Models
- **Message**: Handles chat message data with proper casting and helper methods
  - Stores content, user info, channel, and metadata
  - Provides methods for channel-specific queries
  - Formats data for broadcasting

#### Events
- **ChatMessage**: Unified event for real-time message broadcasting
  - Replaces old TestMessage and ReceiveMessage events
  - Broadcasts to channel-specific rooms
  - Includes formatted message data

#### Controllers
- **ChatController**: Handles HTTP requests for chat operations
  - `sendMessage()`: Creates and broadcasts new messages
  - `getMessages()`: Retrieves message history for channels
  - Includes comprehensive error handling and logging

#### Middleware
- **ChatRateLimit**: Prevents message spam
  - 10 messages per minute per user/IP
  - Returns appropriate HTTP 429 responses
  - Adds rate limit headers

#### Validation
- **SendMessageRequest**: Validates incoming message data
  - Content length and format validation
  - Channel name validation
  - User information validation
  - Custom error messages

### Frontend Components

#### JavaScript
- **app.js**: Consolidated Echo configuration
  - Proper environment variable handling
  - Connection event handlers
  - Error logging

#### Views
- **chat.blade.php**: Modern chat interface
  - Real-time message display
  - Connection status indicators
  - Error handling with toast notifications
  - Responsive design

## Installation & Setup

### Prerequisites
- PHP 8.1+
- Laravel 11+
- Node.js & NPM
- Database (MySQL/PostgreSQL/SQLite)

### Installation Steps

1. **Clone and install dependencies**
   ```bash
   git clone <repository-url>
   cd reverb-project
   composer install
   npm install
   ```

2. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup**
   ```bash
   # Configure your database in .env
   php artisan migrate
   ```

4. **Reverb Configuration**
   Add to your `.env`:
   ```env
   BROADCAST_CONNECTION=reverb
   REVERB_APP_ID=your-app-id
   REVERB_APP_KEY=your-app-key
   REVERB_APP_SECRET=your-app-secret
   REVERB_HOST=localhost
   REVERB_PORT=8080
   REVERB_SCHEME=http
   
   VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
   VITE_REVERB_HOST="${REVERB_HOST}"
   VITE_REVERB_PORT="${REVERB_PORT}"
   VITE_REVERB_SCHEME="${REVERB_SCHEME}"
   ```

5. **Build Assets**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

6. **Start Services**
   ```bash
   # Terminal 1: Laravel development server
   php artisan serve
   
   # Terminal 2: Reverb server
   php artisan reverb:start
   
   # Terminal 3 (optional): Asset watcher
   npm run dev
   ```

## API Endpoints

### Send Message
```http
POST /api/chat/send
Content-Type: application/json

{
  "content": "Hello, world!",
  "channel": "general",
  "user_name": "John Doe",
  "user_id": "123"
}
```

### Get Messages
```http
GET /api/chat/messages/{channel?}
```

### Legacy Endpoint (Backward Compatibility)
```http
POST /send-message
```

## WebSocket Events

### Listening for Messages
```javascript
window.Echo.channel('chat.general')
    .listen('.message.sent', (event) => {
        console.log('New message:', event);
    });
```

### Event Data Structure
```javascript
{
  id: 1,
  content: "Hello, world!",
  user_name: "John Doe",
  user_id: "123",
  channel: "general",
  timestamp: "2024-01-01T12:00:00.000Z",
  formatted_time: "12:00",
  metadata: null
}
```

## Rate Limiting

- **Limit**: 10 messages per minute per user/IP
- **Headers**: 
  - `X-RateLimit-Limit`: Maximum attempts
  - `X-RateLimit-Remaining`: Remaining attempts
  - `X-RateLimit-Reset`: Reset timestamp

## Error Handling

### Backend Errors
- Validation errors return 422 with detailed messages
- Rate limit errors return 429 with retry information
- Server errors return 500 with generic messages
- All errors are logged for debugging

### Frontend Errors
- Connection status indicators
- Toast notifications for errors
- Graceful degradation when WebSocket fails
- Retry mechanisms for failed requests

## Security Features

- **Input Validation**: Comprehensive validation rules
- **Rate Limiting**: Prevents spam and abuse
- **XSS Protection**: HTML escaping in frontend
- **CSRF Protection**: Laravel's built-in CSRF tokens
- **Content Sanitization**: Trim and validate all inputs

## Performance Optimizations

- **Database Indexing**: Optimized queries with proper indexes
- **Message Pagination**: Limit message history loading
- **Connection Pooling**: Efficient WebSocket connections
- **Asset Optimization**: Minified CSS/JS in production

## Testing

```bash
# Run PHP tests
php artisan test

# Run feature tests
php artisan test --filter=ChatTest
```

## Deployment

### Production Checklist
- [ ] Set `APP_ENV=production`
- [ ] Configure proper database
- [ ] Set up SSL for WebSocket connections
- [ ] Configure Redis for broadcasting (optional)
- [ ] Set up process monitoring for Reverb
- [ ] Configure proper logging
- [ ] Set up backup strategies

### Process Management
Use a process manager like Supervisor to keep Reverb running:

```ini
[program:reverb]
command=php /path/to/your/app/artisan reverb:start
directory=/path/to/your/app
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/path/to/your/app/storage/logs/reverb.log
```

## Troubleshooting

### Common Issues

1. **WebSocket Connection Failed**
   - Check Reverb server is running
   - Verify environment variables
   - Check firewall settings

2. **Messages Not Broadcasting**
   - Verify broadcasting driver is set to 'reverb'
   - Check event implementation
   - Review Laravel logs

3. **Rate Limiting Issues**
   - Check middleware configuration
   - Verify cache driver is working
   - Review rate limit settings

### Debug Mode
Enable debug logging in `.env`:
```env
LOG_LEVEL=debug
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).
