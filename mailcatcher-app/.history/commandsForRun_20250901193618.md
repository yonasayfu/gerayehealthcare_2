# Commands Used in the Setup

## Building Frontend Assets
```bash
npm run build
```

## Starting Services

### Main Laravel Application
```bash
cd /Users/yonassayfu/VSProject/BaseBoilerPlate/laravelBoilerPlate
php artisan serve --port=8000
```

### Mailcatcher Application
```bash
cd /Users/yonassayfu/VSProject/BaseBoilerPlate/laravelBoilerPlate/mailcatcher
php artisan serve --port=8001
```

### SMTP Server for Mailcatcher
```bash
cd /Users/yonassayfu/VSProject/BaseBoilerPlate/laravelBoilerPlate/mailcatcher
php artisan mail:catch --port=1025
```

### Vite Development Server
```bash
cd /Users/yonassayfu/VSProject/BaseBoilerPlate/laravelBoilerPlate
npm run dev
```

## Process Management
```bash
# Kill all PHP artisan processes
pkill -f "php artisan"
```

## Port Checking
```bash
# Check which ports are in use
lsof -i :8000,8001,8002,8003,1025 | grep LISTEN
```