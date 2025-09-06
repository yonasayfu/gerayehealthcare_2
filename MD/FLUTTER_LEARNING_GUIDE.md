# 📱 Flutter Learning Guide: From Zero to Hero
## Complete Beginner to Intermediate Guide for Laravel Boilerplate Mobile App

---

## 📚 **Table of Contents**

1. [What is Flutter?](#what-is-flutter)
2. [Understanding Your Project Structure](#understanding-your-project-structure)
3. [Dart Language Basics](#dart-language-basics)
4. [Flutter Widgets Fundamentals](#flutter-widgets-fundamentals)
5. [State Management with Riverpod](#state-management-with-riverpod)
6. [Navigation with GoRouter](#navigation-with-gorouter)
7. [HTTP Requests with Dio](#http-requests-with-dio)
8. [Local Storage with Hive](#local-storage-with-hive)
9. [Clean Architecture Explained](#clean-architecture-explained)
10. [Essential Flutter Commands](#essential-flutter-commands)
11. [Testing in Flutter](#testing-in-flutter)
12. [Deployment Guide](#deployment-guide)

---

## 🚀 **What is Flutter?**

### **Definition**
Flutter is Google's UI toolkit for building natively compiled applications for mobile, web, and desktop from a single codebase.

### **Key Concepts**
- **Cross-Platform**: Write once, run on iOS and Android
- **Dart Language**: Programming language used by Flutter
- **Widgets**: Everything in Flutter is a widget (UI components)
- **Hot Reload**: See changes instantly without restarting the app
- **Native Performance**: Compiles to native ARM code

### **Why Flutter for Your Project?**
- **Single Codebase**: Maintain one codebase for both iOS and Android
- **Fast Development**: Hot reload speeds up development
- **Rich UI**: Beautiful, customizable widgets
- **Growing Ecosystem**: Large community and package ecosystem
- **Google Backing**: Strong support and continuous updates

---

## 📁 **Understanding Your Project Structure**

Let's explore each folder in your `flutter_app` project:

### **Root Level Files**

#### **`pubspec.yaml`** - The Heart of Your Project
```yaml
# This file defines your app's metadata and dependencies
name: laravel_boilerplate_mobile  # Your app's package name
description: A new Flutter project  # App description
version: 1.0.0+1  # Version number + build number

dependencies:  # Packages your app uses
  flutter:
    sdk: flutter
  dio: ^5.4.0  # HTTP client for API calls
  # ... other dependencies

dev_dependencies:  # Packages used only during development
  flutter_test:
    sdk: flutter
  # ... testing and build tools
```

**What you need to know:**
- This is like `package.json` in Node.js or `composer.json` in PHP
- Add new packages here and run `flutter pub get`
- Version numbers follow semantic versioning (major.minor.patch)

#### **`analysis_options.yaml`** - Code Quality Rules
```yaml
# Defines linting rules and code analysis settings
include: package:flutter_lints/flutter.yaml
# This ensures your code follows Flutter best practices
```

### **`lib/` Folder - Your App's Source Code**

#### **`lib/main.dart`** - App Entry Point
```dart
import 'package:flutter/material.dart';

void main() {
  runApp(const MyApp());  // This starts your app
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(  // Root widget of your app
      title: 'Laravel Boilerplate Mobile',
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.deepPurple),
        useMaterial3: true,
      ),
      home: const MyHomePage(title: 'Flutter Demo Home Page'),
    );
  }
}
```

**What you need to know:**
- `main()` function is the entry point (like `index.php` in Laravel)
- `runApp()` starts the Flutter app
- `MaterialApp` is the root widget that provides Material Design

### **Clean Architecture Folders**

#### **`lib/core/`** - Shared Utilities and Constants

**`core/constants/app_constants.dart`**
```dart
class AppConstants {
  static const String appName = 'Laravel Boilerplate Mobile';
  static const String baseUrl = 'http://localhost:8000';
  // All your app-wide constants
}
```
**Purpose**: Store values that don't change (like API URLs, app settings)

**`core/errors/failures.dart`**
```dart
abstract class Failure extends Equatable {
  final String message;
  // Base class for all error types in your app
}
```
**Purpose**: Handle errors consistently across your app

#### **`lib/data/`** - Data Layer (External World)

**`data/datasources/`** - Where data comes from
- **Remote Data Source**: API calls to your Laravel backend
- **Local Data Source**: Local database, cache, shared preferences

**`data/models/`** - Data structures from API
```dart
class UserModel {
  final int id;
  final String name;
  final String email;
  
  // Converts JSON from API to Dart object
  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'],
      name: json['name'],
      email: json['email'],
    );
  }
}
```

**`data/repositories/`** - Implementation of business rules
- Decides whether to get data from API or local storage
- Handles caching logic

#### **`lib/domain/`** - Business Logic Layer

**`domain/entities/`** - Pure business objects
```dart
class User {
  final int id;
  final String name;
  final String email;
  
  // Pure Dart class, no external dependencies
}
```

**`domain/repositories/`** - Contracts/Interfaces
```dart
abstract class UserRepository {
  Future<List<User>> getUsers();
  Future<User> getUserById(int id);
  // Defines what operations are possible
}
```

**`domain/usecases/`** - Business operations
```dart
class GetUserUseCase {
  final UserRepository repository;
  
  Future<User> call(int userId) {
    return repository.getUserById(userId);
  }
}
```

#### **`lib/presentation/`** - UI Layer

**`presentation/pages/`** - Full screens
```dart
class LoginPage extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Login')),
      body: LoginForm(),
    );
  }
}
```

**`presentation/widgets/`** - Reusable UI components
```dart
class CustomButton extends StatelessWidget {
  final String text;
  final VoidCallback onPressed;
  
  @override
  Widget build(BuildContext context) {
    return ElevatedButton(
      onPressed: onPressed,
      child: Text(text),
    );
  }
}
```

**`presentation/providers/`** - State management
```dart
@riverpod
class UserNotifier extends _$UserNotifier {
  @override
  List<User> build() => [];
  
  void addUser(User user) {
    state = [...state, user];
  }
}
```

### **`test/` Folder - Testing**
- **Unit Tests**: Test individual functions/classes
- **Widget Tests**: Test UI components
- **Integration Tests**: Test complete user flows

---

## 🎯 **Dart Language Basics**

### **Variables and Data Types**
```dart
// Variables
String name = 'John Doe';
int age = 25;
double height = 5.9;
bool isActive = true;
List<String> hobbies = ['reading', 'coding'];
Map<String, dynamic> user = {'name': 'John', 'age': 25};

// Null safety (important!)
String? nullableName;  // Can be null
String nonNullableName = 'Must have value';

// Late initialization
late String lateInitialized;
```

### **Functions**
```dart
// Basic function
String greetUser(String name) {
  return 'Hello, $name!';
}

// Arrow function (for simple expressions)
String greetUserShort(String name) => 'Hello, $name!';

// Optional parameters
void printInfo(String name, {int? age, String? city}) {
  print('Name: $name');
  if (age != null) print('Age: $age');
  if (city != null) print('City: $city');
}

// Usage: printInfo('John', age: 25, city: 'New York');
```

### **Classes and Objects**
```dart
class User {
  final String name;
  final int age;
  
  // Constructor
  User({required this.name, required this.age});
  
  // Named constructor
  User.guest() : name = 'Guest', age = 0;
  
  // Method
  void introduce() {
    print('Hi, I\'m $name and I\'m $age years old');
  }
}

// Usage
User user = User(name: 'John', age: 25);
User guest = User.guest();
```

### **Async Programming**
```dart
// Future - represents a value that will be available later
Future<String> fetchUserName() async {
  // Simulate API call
  await Future.delayed(Duration(seconds: 2));
  return 'John Doe';
}

// Using async/await
void getUserInfo() async {
  try {
    String name = await fetchUserName();
    print('User name: $name');
  } catch (error) {
    print('Error: $error');
  }
}

// Stream - continuous data flow
Stream<int> countStream() async* {
  for (int i = 1; i <= 5; i++) {
    await Future.delayed(Duration(seconds: 1));
    yield i;  // Emit value
  }
}
```

---

## 🧩 **Flutter Widgets Fundamentals**

### **Everything is a Widget**
In Flutter, everything you see on screen is a widget. There are two main types:

#### **StatelessWidget** - Immutable widgets
```dart
class WelcomeText extends StatelessWidget {
  final String name;
  
  const WelcomeText({Key? key, required this.name}) : super(key: key);
  
  @override
  Widget build(BuildContext context) {
    return Text('Welcome, $name!');
  }
}
```
**Use when**: The widget doesn't need to change after it's built

#### **StatefulWidget** - Mutable widgets
```dart
class Counter extends StatefulWidget {
  @override
  _CounterState createState() => _CounterState();
}

class _CounterState extends State<Counter> {
  int _count = 0;
  
  void _increment() {
    setState(() {  // Tells Flutter to rebuild the widget
      _count++;
    });
  }
  
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Text('Count: $_count'),
        ElevatedButton(
          onPressed: _increment,
          child: Text('Increment'),
        ),
      ],
    );
  }
}
```
**Use when**: The widget needs to change based on user interaction or data updates

### **Common Widgets You'll Use**

#### **Layout Widgets**
```dart
// Column - arranges children vertically
Column(
  children: [
    Text('First'),
    Text('Second'),
    Text('Third'),
  ],
)

// Row - arranges children horizontally
Row(
  children: [
    Icon(Icons.star),
    Text('Rating'),
    Text('4.5'),
  ],
)

// Container - box model with padding, margin, decoration
Container(
  padding: EdgeInsets.all(16),
  margin: EdgeInsets.symmetric(horizontal: 8),
  decoration: BoxDecoration(
    color: Colors.blue,
    borderRadius: BorderRadius.circular(8),
  ),
  child: Text('Styled container'),
)

// Scaffold - basic page structure
Scaffold(
  appBar: AppBar(title: Text('My App')),
  body: Center(child: Text('Hello World')),
  floatingActionButton: FloatingActionButton(
    onPressed: () {},
    child: Icon(Icons.add),
  ),
)
```

#### **Input Widgets**
```dart
// TextField - text input
TextField(
  decoration: InputDecoration(
    labelText: 'Enter your name',
    border: OutlineInputBorder(),
  ),
  onChanged: (value) {
    print('User typed: $value');
  },
)

// ElevatedButton - raised button
ElevatedButton(
  onPressed: () {
    print('Button pressed!');
  },
  child: Text('Click Me'),
)

// Switch - toggle switch
Switch(
  value: isEnabled,
  onChanged: (bool value) {
    setState(() {
      isEnabled = value;
    });
  },
)
```

#### **Display Widgets**
```dart
// Text - display text
Text(
  'Hello World',
  style: TextStyle(
    fontSize: 24,
    fontWeight: FontWeight.bold,
    color: Colors.blue,
  ),
)

// Image - display images
Image.network('https://example.com/image.jpg')
Image.asset('assets/images/logo.png')

// Icon - display icons
Icon(
  Icons.favorite,
  color: Colors.red,
  size: 24,
)

// ListView - scrollable list
ListView.builder(
  itemCount: items.length,
  itemBuilder: (context, index) {
    return ListTile(
      title: Text(items[index].title),
      subtitle: Text(items[index].description),
      onTap: () {
        // Handle item tap
      },
    );
  },
)
```

---

## 🔄 **State Management with Riverpod**

Riverpod is a state management solution that helps you manage data across your app.

### **Why Riverpod?**
- **Type Safe**: Compile-time error checking
- **Testable**: Easy to test providers
- **No BuildContext**: Access providers anywhere
- **Auto-dispose**: Automatically cleans up unused state

### **Basic Provider Types**

#### **Provider** - For immutable values
```dart
@riverpod
String appTitle(AppTitleRef ref) {
  return 'Laravel Boilerplate Mobile';
}

// Usage in widget
class MyWidget extends ConsumerWidget {
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final title = ref.watch(appTitleProvider);
    return Text(title);
  }
}
```

#### **StateProvider** - For simple mutable state
```dart
final counterProvider = StateProvider<int>((ref) => 0);

// Usage
class CounterWidget extends ConsumerWidget {
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final count = ref.watch(counterProvider);
    
    return Column(
      children: [
        Text('Count: $count'),
        ElevatedButton(
          onPressed: () => ref.read(counterProvider.notifier).state++,
          child: Text('Increment'),
        ),
      ],
    );
  }
}
```

#### **AsyncNotifierProvider** - For complex async state
```dart
@riverpod
class UserNotifier extends _$UserNotifier {
  @override
  Future<List<User>> build() async {
    // Load initial data
    return await _userRepository.getUsers();
  }
  
  Future<void> addUser(User user) async {
    state = const AsyncValue.loading();
    try {
      await _userRepository.createUser(user);
      final users = await _userRepository.getUsers();
      state = AsyncValue.data(users);
    } catch (error) {
      state = AsyncValue.error(error, StackTrace.current);
    }
  }
}

// Usage
class UserListWidget extends ConsumerWidget {
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final usersAsync = ref.watch(userNotifierProvider);
    
    return usersAsync.when(
      data: (users) => ListView.builder(
        itemCount: users.length,
        itemBuilder: (context, index) => ListTile(
          title: Text(users[index].name),
        ),
      ),
      loading: () => CircularProgressIndicator(),
      error: (error, stack) => Text('Error: $error'),
    );
  }
}
```

### **Provider Dependencies**
```dart
@riverpod
Future<User> currentUser(CurrentUserRef ref) async {
  // This provider depends on authProvider
  final authState = ref.watch(authProvider);
  if (authState.isAuthenticated) {
    return await _userRepository.getCurrentUser();
  }
  throw Exception('Not authenticated');
}
```

---

## 🧭 **Navigation with GoRouter**

GoRouter provides declarative routing for your Flutter app.

### **Basic Setup**
```dart
final _router = GoRouter(
  routes: [
    GoRoute(
      path: '/',
      builder: (context, state) => const HomePage(),
    ),
    GoRoute(
      path: '/login',
      builder: (context, state) => const LoginPage(),
    ),
    GoRoute(
      path: '/profile/:userId',
      builder: (context, state) {
        final userId = state.pathParameters['userId']!;
        return ProfilePage(userId: userId);
      },
    ),
  ],
);

// In your MaterialApp
MaterialApp.router(
  routerConfig: _router,
)
```

### **Navigation Methods**
```dart
// Navigate to a route
context.go('/login');

// Navigate with parameters
context.go('/profile/123');

// Push a new route (can go back)
context.push('/settings');

// Replace current route
context.replace('/dashboard');

// Go back
context.pop();

// Go back with result
context.pop('result data');
```

### **Route Parameters**
```dart
// Path parameters
GoRoute(
  path: '/user/:id/posts/:postId',
  builder: (context, state) {
    final userId = state.pathParameters['id']!;
    final postId = state.pathParameters['postId']!;
    return PostDetailPage(userId: userId, postId: postId);
  },
)

// Query parameters
GoRoute(
  path: '/search',
  builder: (context, state) {
    final query = state.uri.queryParameters['q'] ?? '';
    final category = state.uri.queryParameters['category'];
    return SearchPage(query: query, category: category);
  },
)

// Navigate with query parameters
context.go('/search?q=flutter&category=mobile');
```

### **Route Guards (Authentication)**
```dart
final _router = GoRouter(
  redirect: (context, state) {
    final isAuthenticated = /* check auth state */;
    final isLoginRoute = state.matchedLocation == '/login';
    
    if (!isAuthenticated && !isLoginRoute) {
      return '/login';
    }
    if (isAuthenticated && isLoginRoute) {
      return '/';
    }
    return null; // No redirect needed
  },
  routes: [
    // Your routes
  ],
);
```

---

## 🌐 **HTTP Requests with Dio**

Dio is a powerful HTTP client for Dart that makes API calls easy.

### **Basic Setup**
```dart
// Create Dio instance
final dio = Dio(BaseOptions(
  baseUrl: 'http://localhost:8000/api/v1',
  connectTimeout: Duration(seconds: 30),
  receiveTimeout: Duration(seconds: 30),
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
));

// Add interceptors for logging and auth
dio.interceptors.add(LogInterceptor(
  requestBody: true,
  responseBody: true,
));

dio.interceptors.add(InterceptorsWrapper(
  onRequest: (options, handler) {
    // Add auth token to requests
    final token = getStoredToken();
    if (token != null) {
      options.headers['Authorization'] = 'Bearer $token';
    }
    handler.next(options);
  },
  onError: (error, handler) {
    // Handle 401 unauthorized
    if (error.response?.statusCode == 401) {
      // Redirect to login
      logout();
    }
    handler.next(error);
  },
));
```

### **Making API Calls**
```dart
class UserApiService {
  final Dio _dio;

  UserApiService(this._dio);

  // GET request
  Future<List<User>> getUsers() async {
    try {
      final response = await _dio.get('/users');
      final List<dynamic> data = response.data['data'];
      return data.map((json) => User.fromJson(json)).toList();
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  // POST request
  Future<User> createUser(CreateUserRequest request) async {
    try {
      final response = await _dio.post(
        '/users',
        data: request.toJson(),
      );
      return User.fromJson(response.data['data']);
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  // PUT request
  Future<User> updateUser(int id, UpdateUserRequest request) async {
    try {
      final response = await _dio.put(
        '/users/$id',
        data: request.toJson(),
      );
      return User.fromJson(response.data['data']);
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  // DELETE request
  Future<void> deleteUser(int id) async {
    try {
      await _dio.delete('/users/$id');
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  // File upload
  Future<String> uploadAvatar(File file) async {
    try {
      final formData = FormData.fromMap({
        'avatar': await MultipartFile.fromFile(
          file.path,
          filename: file.path.split('/').last,
        ),
      });

      final response = await _dio.post(
        '/users/avatar',
        data: formData,
      );

      return response.data['data']['url'];
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  Exception _handleError(DioException e) {
    switch (e.type) {
      case DioExceptionType.connectionTimeout:
      case DioExceptionType.receiveTimeout:
        return TimeoutException(message: 'Request timed out');
      case DioExceptionType.badResponse:
        final statusCode = e.response?.statusCode;
        final message = e.response?.data['message'] ?? 'Server error';
        return ServerException(message: message, code: statusCode);
      case DioExceptionType.connectionError:
        return NetworkException(message: 'No internet connection');
      default:
        return UnknownException(message: 'Something went wrong');
    }
  }
}
```

### **Using with Riverpod**
```dart
@riverpod
UserApiService userApiService(UserApiServiceRef ref) {
  final dio = ref.watch(dioProvider);
  return UserApiService(dio);
}

@riverpod
class UsersNotifier extends _$UsersNotifier {
  @override
  Future<List<User>> build() async {
    final apiService = ref.read(userApiServiceProvider);
    return await apiService.getUsers();
  }

  Future<void> createUser(CreateUserRequest request) async {
    state = const AsyncValue.loading();
    try {
      final apiService = ref.read(userApiServiceProvider);
      await apiService.createUser(request);
      // Refresh the list
      ref.invalidateSelf();
    } catch (error) {
      state = AsyncValue.error(error, StackTrace.current);
    }
  }
}
```

---

## 💾 **Local Storage with Hive**

Hive is a fast, lightweight NoSQL database for Flutter.

### **Setup and Initialization**
```dart
// In main.dart
void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Initialize Hive
  await Hive.initFlutter();

  // Register adapters for custom objects
  Hive.registerAdapter(UserAdapter());
  Hive.registerAdapter(MessageAdapter());

  // Open boxes
  await Hive.openBox<User>('users');
  await Hive.openBox<Message>('messages');
  await Hive.openBox('settings');

  runApp(MyApp());
}
```

### **Creating Type Adapters**
```dart
// user.dart
@HiveType(typeId: 0)
class User extends HiveObject {
  @HiveField(0)
  final int id;

  @HiveField(1)
  final String name;

  @HiveField(2)
  final String email;

  User({required this.id, required this.name, required this.email});
}

// Generate adapter with: flutter packages pub run build_runner build
```

### **Basic Operations**
```dart
class LocalStorageService {
  // Simple key-value storage
  Future<void> saveString(String key, String value) async {
    final box = Hive.box('settings');
    await box.put(key, value);
  }

  String? getString(String key) {
    final box = Hive.box('settings');
    return box.get(key);
  }

  // Object storage
  Future<void> saveUser(User user) async {
    final box = Hive.box<User>('users');
    await box.put(user.id, user);
  }

  User? getUser(int id) {
    final box = Hive.box<User>('users');
    return box.get(id);
  }

  List<User> getAllUsers() {
    final box = Hive.box<User>('users');
    return box.values.toList();
  }

  Future<void> deleteUser(int id) async {
    final box = Hive.box<User>('users');
    await box.delete(id);
  }

  // Clear all data
  Future<void> clearAllUsers() async {
    final box = Hive.box<User>('users');
    await box.clear();
  }
}
```

### **Secure Storage for Sensitive Data**
```dart
class SecureStorageService {
  final FlutterSecureStorage _storage = FlutterSecureStorage(
    aOptions: AndroidOptions(
      encryptedSharedPreferences: true,
    ),
    iOptions: IOSOptions(
      accessibility: IOSAccessibility.first_unlock_this_device,
    ),
  );

  Future<void> saveToken(String token) async {
    await _storage.write(key: 'auth_token', value: token);
  }

  Future<String?> getToken() async {
    return await _storage.read(key: 'auth_token');
  }

  Future<void> deleteToken() async {
    await _storage.delete(key: 'auth_token');
  }

  Future<void> clearAll() async {
    await _storage.deleteAll();
  }
}
```

---

## 🏗️ **Clean Architecture Explained**

Clean Architecture separates your code into layers with clear responsibilities.

### **The Layers (Outside to Inside)**

#### **1. Presentation Layer** (UI)
- **Responsibility**: Display data and handle user interactions
- **Components**: Pages, Widgets, State Management (Riverpod providers)
- **Rules**: Can only depend on Domain layer

```dart
// presentation/pages/user_list_page.dart
class UserListPage extends ConsumerWidget {
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final usersAsync = ref.watch(usersNotifierProvider);

    return Scaffold(
      appBar: AppBar(title: Text('Users')),
      body: usersAsync.when(
        data: (users) => ListView.builder(
          itemCount: users.length,
          itemBuilder: (context, index) => UserTile(user: users[index]),
        ),
        loading: () => Center(child: CircularProgressIndicator()),
        error: (error, _) => Center(child: Text('Error: $error')),
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => context.push('/users/create'),
        child: Icon(Icons.add),
      ),
    );
  }
}

// presentation/providers/users_provider.dart
@riverpod
class UsersNotifier extends _$UsersNotifier {
  @override
  Future<List<User>> build() async {
    final useCase = ref.read(getUsersUseCaseProvider);
    final result = await useCase();
    return result.fold(
      (failure) => throw failure,
      (users) => users,
    );
  }

  Future<void> createUser(String name, String email) async {
    final useCase = ref.read(createUserUseCaseProvider);
    final result = await useCase(CreateUserParams(name: name, email: email));

    result.fold(
      (failure) => throw failure,
      (_) => ref.invalidateSelf(), // Refresh the list
    );
  }
}
```

#### **2. Domain Layer** (Business Logic)
- **Responsibility**: Contains business rules and entities
- **Components**: Entities, Use Cases, Repository Interfaces
- **Rules**: No dependencies on external layers

```dart
// domain/entities/user.dart
class User extends Equatable {
  final int id;
  final String name;
  final String email;
  final DateTime createdAt;

  const User({
    required this.id,
    required this.name,
    required this.email,
    required this.createdAt,
  });

  @override
  List<Object> get props => [id, name, email, createdAt];
}

// domain/repositories/user_repository.dart
abstract class UserRepository {
  Future<Either<Failure, List<User>>> getUsers();
  Future<Either<Failure, User>> getUserById(int id);
  Future<Either<Failure, User>> createUser(String name, String email);
  Future<Either<Failure, void>> deleteUser(int id);
}

// domain/usecases/get_users_usecase.dart
class GetUsersUseCase {
  final UserRepository repository;

  GetUsersUseCase(this.repository);

  Future<Either<Failure, List<User>>> call() async {
    return await repository.getUsers();
  }
}

// domain/usecases/create_user_usecase.dart
class CreateUserUseCase {
  final UserRepository repository;

  CreateUserUseCase(this.repository);

  Future<Either<Failure, User>> call(CreateUserParams params) async {
    return await repository.createUser(params.name, params.email);
  }
}

class CreateUserParams extends Equatable {
  final String name;
  final String email;

  const CreateUserParams({required this.name, required this.email});

  @override
  List<Object> get props => [name, email];
}
```

#### **3. Data Layer** (External Data)
- **Responsibility**: Fetch data from external sources
- **Components**: Models, Data Sources, Repository Implementations
- **Rules**: Can depend on Domain layer

```dart
// data/models/user_model.dart
class UserModel extends User {
  const UserModel({
    required super.id,
    required super.name,
    required super.email,
    required super.createdAt,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      createdAt: DateTime.parse(json['created_at']),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'created_at': createdAt.toIso8601String(),
    };
  }
}

// data/datasources/user_remote_datasource.dart
abstract class UserRemoteDataSource {
  Future<List<UserModel>> getUsers();
  Future<UserModel> getUserById(int id);
  Future<UserModel> createUser(String name, String email);
  Future<void> deleteUser(int id);
}

class UserRemoteDataSourceImpl implements UserRemoteDataSource {
  final Dio dio;

  UserRemoteDataSourceImpl(this.dio);

  @override
  Future<List<UserModel>> getUsers() async {
    try {
      final response = await dio.get('/users');
      final List<dynamic> data = response.data['data'];
      return data.map((json) => UserModel.fromJson(json)).toList();
    } on DioException catch (e) {
      throw ServerException(message: e.message ?? 'Server error');
    }
  }

  @override
  Future<UserModel> createUser(String name, String email) async {
    try {
      final response = await dio.post('/users', data: {
        'name': name,
        'email': email,
      });
      return UserModel.fromJson(response.data['data']);
    } on DioException catch (e) {
      throw ServerException(message: e.message ?? 'Server error');
    }
  }

  // ... other methods
}

// data/repositories/user_repository_impl.dart
class UserRepositoryImpl implements UserRepository {
  final UserRemoteDataSource remoteDataSource;
  final UserLocalDataSource localDataSource;
  final NetworkInfo networkInfo;

  UserRepositoryImpl({
    required this.remoteDataSource,
    required this.localDataSource,
    required this.networkInfo,
  });

  @override
  Future<Either<Failure, List<User>>> getUsers() async {
    if (await networkInfo.isConnected) {
      try {
        final users = await remoteDataSource.getUsers();
        // Cache the data locally
        await localDataSource.cacheUsers(users);
        return Right(users);
      } on ServerException catch (e) {
        return Left(ServerFailure(message: e.message));
      }
    } else {
      try {
        final users = await localDataSource.getCachedUsers();
        return Right(users);
      } on CacheException catch (e) {
        return Left(CacheFailure(message: e.message));
      }
    }
  }

  @override
  Future<Either<Failure, User>> createUser(String name, String email) async {
    if (await networkInfo.isConnected) {
      try {
        final user = await remoteDataSource.createUser(name, email);
        return Right(user);
      } on ServerException catch (e) {
        return Left(ServerFailure(message: e.message));
      }
    } else {
      return Left(NetworkFailure(message: 'No internet connection'));
    }
  }
}
```

### **Dependency Injection Setup**
```dart
// core/di/injection.dart
@InjectableInit()
void configureDependencies() => getIt.init();

final getIt = GetIt.instance;

@module
abstract class RegisterModule {
  @lazySingleton
  Dio get dio => Dio(BaseOptions(
    baseUrl: AppConstants.apiBaseUrl,
    connectTimeout: AppConstants.connectTimeout,
    receiveTimeout: AppConstants.receiveTimeout,
  ));

  @lazySingleton
  FlutterSecureStorage get secureStorage => FlutterSecureStorage();
}

// In main.dart
void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  configureDependencies();
  runApp(MyApp());
}
```

## 💻 **Essential Flutter Commands**

### **Project Management**
```bash
# Create new Flutter project
flutter create my_app
flutter create --org com.example my_app

# Get dependencies
flutter pub get

# Update dependencies
flutter pub upgrade

# Add a new package
flutter pub add package_name
flutter pub add dio
flutter pub add --dev build_runner

# Remove a package
flutter pub remove package_name

# Check for outdated packages
flutter pub outdated

# Clean build files
flutter clean

# Check Flutter installation
flutter doctor
flutter doctor -v
```

### **Development Commands**
```bash
# Run app on connected device
flutter run

# Run on specific device
flutter run -d device_id
flutter run -d ios
flutter run -d android

# Run in debug mode (default)
flutter run --debug

# Run in profile mode (performance testing)
flutter run --profile

# Run in release mode
flutter run --release

# Hot reload (r in terminal while running)
# Hot restart (R in terminal while running)

# List connected devices
flutter devices

# List available emulators
flutter emulators

# Launch emulator
flutter emulators --launch emulator_id
```

### **Build Commands**
```bash
# Build APK (Android)
flutter build apk
flutter build apk --release
flutter build apk --debug
flutter build apk --split-per-abi  # Smaller APKs

# Build App Bundle (Android - recommended for Play Store)
flutter build appbundle
flutter build appbundle --release

# Build iOS app
flutter build ios
flutter build ios --release

# Build for web
flutter build web

# Build for desktop
flutter build windows
flutter build macos
flutter build linux
```

### **Code Generation**
```bash
# Generate code (for json_serializable, riverpod_generator, etc.)
flutter packages pub run build_runner build

# Watch for changes and auto-generate
flutter packages pub run build_runner watch

# Clean generated files and rebuild
flutter packages pub run build_runner build --delete-conflicting-outputs
```

### **Testing Commands**
```bash
# Run all tests
flutter test

# Run specific test file
flutter test test/widget_test.dart

# Run tests with coverage
flutter test --coverage

# Run integration tests
flutter drive --target=test_driver/app.dart

# Run tests on device
flutter test integration_test/
```

### **Analysis and Formatting**
```bash
# Analyze code for issues
flutter analyze

# Format code
flutter format .
flutter format lib/

# Check formatting without changing files
flutter format --dry-run .
```

### **Debugging Commands**
```bash
# Enable debugging
flutter run --debug

# Attach debugger to running app
flutter attach

# Take screenshot
flutter screenshot

# Get app logs
flutter logs

# Profile app performance
flutter run --profile
```

---

## 🧪 **Testing in Flutter**

Testing ensures your app works correctly and prevents regressions.

### **Types of Tests**

#### **1. Unit Tests** - Test individual functions/classes
```dart
// test/services/user_service_test.dart
import 'package:flutter_test/flutter_test.dart';
import 'package:mockito/mockito.dart';
import 'package:mockito/annotations.dart';

import 'package:my_app/services/user_service.dart';
import 'package:my_app/repositories/user_repository.dart';

@GenerateMocks([UserRepository])
import 'user_service_test.mocks.dart';

void main() {
  group('UserService', () {
    late UserService userService;
    late MockUserRepository mockRepository;

    setUp(() {
      mockRepository = MockUserRepository();
      userService = UserService(mockRepository);
    });

    test('should return users when repository call is successful', () async {
      // Arrange
      final users = [
        User(id: 1, name: 'John', email: 'john@example.com'),
        User(id: 2, name: 'Jane', email: 'jane@example.com'),
      ];
      when(mockRepository.getUsers()).thenAnswer((_) async => Right(users));

      // Act
      final result = await userService.getUsers();

      // Assert
      expect(result.isRight(), true);
      result.fold(
        (failure) => fail('Expected success but got failure'),
        (actualUsers) => expect(actualUsers, equals(users)),
      );
      verify(mockRepository.getUsers()).called(1);
    });

    test('should return failure when repository call fails', () async {
      // Arrange
      final failure = ServerFailure(message: 'Server error');
      when(mockRepository.getUsers()).thenAnswer((_) async => Left(failure));

      // Act
      final result = await userService.getUsers();

      // Assert
      expect(result.isLeft(), true);
      result.fold(
        (actualFailure) => expect(actualFailure, equals(failure)),
        (users) => fail('Expected failure but got success'),
      );
    });
  });
}
```

#### **2. Widget Tests** - Test UI components
```dart
// test/widgets/user_tile_test.dart
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';

import 'package:my_app/widgets/user_tile.dart';
import 'package:my_app/models/user.dart';

void main() {
  group('UserTile', () {
    testWidgets('should display user information', (WidgetTester tester) async {
      // Arrange
      final user = User(
        id: 1,
        name: 'John Doe',
        email: 'john@example.com',
        createdAt: DateTime.now(),
      );

      // Act
      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: UserTile(user: user),
          ),
        ),
      );

      // Assert
      expect(find.text('John Doe'), findsOneWidget);
      expect(find.text('john@example.com'), findsOneWidget);
      expect(find.byType(ListTile), findsOneWidget);
    });

    testWidgets('should call onTap when tapped', (WidgetTester tester) async {
      // Arrange
      final user = User(id: 1, name: 'John', email: 'john@example.com');
      bool wasTapped = false;

      // Act
      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: UserTile(
              user: user,
              onTap: () => wasTapped = true,
            ),
          ),
        ),
      );

      await tester.tap(find.byType(UserTile));

      // Assert
      expect(wasTapped, true);
    });
  });
}
```

#### **3. Integration Tests** - Test complete user flows
```dart
// integration_test/app_test.dart
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:integration_test/integration_test.dart';

import 'package:my_app/main.dart' as app;

void main() {
  IntegrationTestWidgetsFlutterBinding.ensureInitialized();

  group('App Integration Tests', () {
    testWidgets('complete login flow', (WidgetTester tester) async {
      // Start the app
      app.main();
      await tester.pumpAndSettle();

      // Navigate to login page
      await tester.tap(find.text('Login'));
      await tester.pumpAndSettle();

      // Enter credentials
      await tester.enterText(find.byKey(Key('email_field')), 'test@example.com');
      await tester.enterText(find.byKey(Key('password_field')), 'password123');

      // Tap login button
      await tester.tap(find.text('Login'));
      await tester.pumpAndSettle();

      // Verify successful login
      expect(find.text('Welcome'), findsOneWidget);
    });

    testWidgets('create and delete user flow', (WidgetTester tester) async {
      app.main();
      await tester.pumpAndSettle();

      // Navigate to users page
      await tester.tap(find.text('Users'));
      await tester.pumpAndSettle();

      // Tap add user button
      await tester.tap(find.byType(FloatingActionButton));
      await tester.pumpAndSettle();

      // Fill user form
      await tester.enterText(find.byKey(Key('name_field')), 'Test User');
      await tester.enterText(find.byKey(Key('email_field')), 'test@example.com');

      // Submit form
      await tester.tap(find.text('Create User'));
      await tester.pumpAndSettle();

      // Verify user was created
      expect(find.text('Test User'), findsOneWidget);

      // Delete user
      await tester.tap(find.byIcon(Icons.delete));
      await tester.pumpAndSettle();

      // Confirm deletion
      await tester.tap(find.text('Delete'));
      await tester.pumpAndSettle();

      // Verify user was deleted
      expect(find.text('Test User'), findsNothing);
    });
  });
}
```

### **Golden Tests** - Visual regression testing
```dart
// test/golden/user_tile_golden_test.dart
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';

import 'package:my_app/widgets/user_tile.dart';

void main() {
  group('UserTile Golden Tests', () {
    testWidgets('user tile matches golden file', (WidgetTester tester) async {
      final user = User(
        id: 1,
        name: 'John Doe',
        email: 'john@example.com',
      );

      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: UserTile(user: user),
          ),
        ),
      );

      await expectLater(
        find.byType(UserTile),
        matchesGoldenFile('user_tile.png'),
      );
    });
  });
}

// Run with: flutter test --update-goldens
```

### **Test Coverage**
```bash
# Generate coverage report
flutter test --coverage

# View coverage in browser (requires lcov)
genhtml coverage/lcov.info -o coverage/html
open coverage/html/index.html
```

## 🚀 **Deployment Guide**

### **📱 Android Deployment (Google Play Store)**

#### **Step 1: Prepare Your App**

**1.1 Update App Information**
```yaml
# pubspec.yaml
name: laravel_boilerplate_mobile
description: A comprehensive mobile app for Laravel boilerplate
version: 1.0.0+1  # version+build_number

flutter:
  uses-material-design: true
```

**1.2 Configure App Icons**
```bash
# Install flutter_launcher_icons
flutter pub add --dev flutter_launcher_icons

# Add to pubspec.yaml
flutter_icons:
  android: "launcher_icon"
  ios: true
  image_path: "assets/icon/icon.png"
  min_sdk_android: 21

# Generate icons
flutter pub run flutter_launcher_icons:main
```

**1.3 Update Android Configuration**
```kotlin
// android/app/src/main/kotlin/com/example/app/MainActivity.kt
package com.laravelboilerplate.laravel_boilerplate_mobile

import io.flutter.embedding.android.FlutterActivity

class MainActivity: FlutterActivity() {
}
```

```xml
<!-- android/app/src/main/AndroidManifest.xml -->
<manifest xmlns:android="http://schemas.android.com/apk/res/android"
    package="com.laravelboilerplate.laravel_boilerplate_mobile">

    <uses-permission android:name="android.permission.INTERNET" />
    <uses-permission android:name="android.permission.CAMERA" />
    <uses-permission android:name="android.permission.READ_EXTERNAL_STORAGE" />
    <uses-permission android:name="android.permission.WRITE_EXTERNAL_STORAGE" />

    <application
        android:label="Laravel Boilerplate"
        android:name="${applicationName}"
        android:icon="@mipmap/launcher_icon">

        <activity
            android:name=".MainActivity"
            android:exported="true"
            android:launchMode="singleTop"
            android:theme="@style/LaunchTheme"
            android:configChanges="orientation|keyboardHidden|keyboard|screenSize|smallestScreenSize|locale|layoutDirection|fontScale|screenLayout|density|uiMode"
            android:hardwareAccelerated="true"
            android:windowSoftInputMode="adjustResize">

            <meta-data
              android:name="io.flutter.embedding.android.NormalTheme"
              android:resource="@style/NormalTheme" />

            <intent-filter android:autoVerify="true">
                <action android:name="android.intent.action.MAIN"/>
                <category android:name="android.intent.category.LAUNCHER"/>
            </intent-filter>
        </activity>

        <meta-data
            android:name="flutterEmbedding"
            android:value="2" />
    </application>
</manifest>
```

#### **Step 2: Create Signing Key**

**2.1 Generate Keystore**
```bash
# Create keystore (do this once and keep it safe!)
keytool -genkey -v -keystore ~/upload-keystore.jks -keyalg RSA -keysize 2048 -validity 10000 -alias upload

# You'll be prompted for:
# - Keystore password (remember this!)
# - Key password (remember this!)
# - Your name, organization, city, state, country
```

**2.2 Configure Signing**
```properties
# android/key.properties (create this file)
storePassword=your_keystore_password
keyPassword=your_key_password
keyAlias=upload
storeFile=/Users/yourusername/upload-keystore.jks
```

```gradle
// android/app/build.gradle
def keystoreProperties = new Properties()
def keystorePropertiesFile = rootProject.file('key.properties')
if (keystorePropertiesFile.exists()) {
    keystoreProperties.load(new FileInputStream(keystorePropertiesFile))
}

android {
    compileSdkVersion flutter.compileSdkVersion
    ndkVersion flutter.ndkVersion

    compileOptions {
        sourceCompatibility JavaVersion.VERSION_1_8
        targetCompatibility JavaVersion.VERSION_1_8
    }

    defaultConfig {
        applicationId "com.laravelboilerplate.laravel_boilerplate_mobile"
        minSdkVersion flutter.minSdkVersion
        targetSdkVersion flutter.targetSdkVersion
        versionCode flutterVersionCode.toInteger()
        versionName flutterVersionName
    }

    signingConfigs {
        release {
            keyAlias keystoreProperties['keyAlias']
            keyPassword keystoreProperties['keyPassword']
            storeFile keystoreProperties['storeFile'] ? file(keystoreProperties['storeFile']) : null
            storePassword keystoreProperties['storePassword']
        }
    }

    buildTypes {
        release {
            signingConfig signingConfigs.release
            minifyEnabled true
            shrinkResources true
            proguardFiles getDefaultProguardFile('proguard-android-optimize.txt'), 'proguard-rules.pro'
        }
    }
}
```

#### **Step 3: Build Release APK/Bundle**

**3.1 Build App Bundle (Recommended)**
```bash
# Clean previous builds
flutter clean
flutter pub get

# Build release app bundle
flutter build appbundle --release

# Output: build/app/outputs/bundle/release/app-release.aab
```

**3.2 Build APK (Alternative)**
```bash
# Build release APK
flutter build apk --release

# Build split APKs (smaller size)
flutter build apk --split-per-abi --release

# Output: build/app/outputs/flutter-apk/app-release.apk
```

#### **Step 4: Test Release Build**
```bash
# Install release APK on device
flutter install --release

# Or install specific APK
adb install build/app/outputs/flutter-apk/app-release.apk
```

#### **Step 5: Upload to Google Play Console**

**5.1 Create Google Play Console Account**
- Go to [Google Play Console](https://play.google.com/console)
- Pay $25 one-time registration fee
- Complete account verification

**5.2 Create New App**
- Click "Create app"
- Fill in app details:
  - App name: "Laravel Boilerplate Mobile"
  - Default language: English
  - App or game: App
  - Free or paid: Free (or Paid)

**5.3 Complete App Information**
- **App content**: Privacy policy, target audience, content rating
- **Store listing**: Description, screenshots, graphics
- **App releases**: Upload your AAB file

**5.4 Upload App Bundle**
- Go to "Release" → "Production"
- Click "Create new release"
- Upload your `app-release.aab` file
- Add release notes
- Review and rollout

**5.5 Required Assets**
```
Screenshots:
- Phone: 2-8 screenshots (16:9 or 9:16 ratio)
- 7-inch tablet: 1-8 screenshots
- 10-inch tablet: 1-8 screenshots

Graphics:
- High-res icon: 512x512 PNG
- Feature graphic: 1024x500 JPG/PNG
- Promo video: YouTube URL (optional)
```

---

### **🍎 iOS Deployment (Apple App Store)**

#### **Step 1: Prepare Your App**

**1.1 Update iOS Configuration**
```xml
<!-- ios/Runner/Info.plist -->
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>CFBundleDevelopmentRegion</key>
    <string>$(DEVELOPMENT_LANGUAGE)</string>
    <key>CFBundleDisplayName</key>
    <string>Laravel Boilerplate</string>
    <key>CFBundleExecutable</key>
    <string>$(EXECUTABLE_NAME)</string>
    <key>CFBundleIdentifier</key>
    <string>$(PRODUCT_BUNDLE_IDENTIFIER)</string>
    <key>CFBundleInfoDictionaryVersion</key>
    <string>6.0</string>
    <key>CFBundleName</key>
    <string>laravel_boilerplate_mobile</string>
    <key>CFBundlePackageType</key>
    <string>APPL</string>
    <key>CFBundleShortVersionString</key>
    <string>$(FLUTTER_BUILD_NAME)</string>
    <key>CFBundleSignature</key>
    <string>????</string>
    <key>CFBundleVersion</key>
    <string>$(FLUTTER_BUILD_NUMBER)</string>
    <key>LSRequiresIPhoneOS</key>
    <true/>
    <key>UILaunchStoryboardName</key>
    <string>LaunchScreen</string>
    <key>UIMainStoryboardFile</key>
    <string>Main</string>
    <key>UISupportedInterfaceOrientations</key>
    <array>
        <string>UIInterfaceOrientationPortrait</string>
        <string>UIInterfaceOrientationLandscapeLeft</string>
        <string>UIInterfaceOrientationLandscapeRight</string>
    </array>
    <key>UISupportedInterfaceOrientations~ipad</key>
    <array>
        <string>UIInterfaceOrientationPortrait</string>
        <string>UIInterfaceOrientationPortraitUpsideDown</string>
        <string>UIInterfaceOrientationLandscapeLeft</string>
        <string>UIInterfaceOrientationLandscapeRight</string>
    </array>
    <key>UIViewControllerBasedStatusBarAppearance</key>
    <false/>

    <!-- Permissions -->
    <key>NSCameraUsageDescription</key>
    <string>This app needs camera access to take photos</string>
    <key>NSPhotoLibraryUsageDescription</key>
    <string>This app needs photo library access to select images</string>
    <key>NSMicrophoneUsageDescription</key>
    <string>This app needs microphone access for voice messages</string>
</dict>
</plist>
```

#### **Step 2: Apple Developer Account Setup**

**2.1 Create Apple Developer Account**
- Go to [Apple Developer](https://developer.apple.com)
- Enroll in Apple Developer Program ($99/year)
- Complete verification process

**2.2 Create App ID**
- Go to Apple Developer Console
- Certificates, Identifiers & Profiles
- Identifiers → App IDs
- Create new App ID: `com.laravelboilerplate.laravel_boilerplate_mobile`

**2.3 Create Certificates**
```bash
# Open Xcode and go to Preferences → Accounts
# Add your Apple ID
# Download Manual Profiles

# Or use Xcode to automatically manage signing
```

#### **Step 3: Configure Xcode Project**

**3.1 Open iOS Project in Xcode**
```bash
# Open iOS project
open ios/Runner.xcworkspace
```

**3.2 Configure Signing & Capabilities**
- Select Runner project
- Go to "Signing & Capabilities"
- Team: Select your development team
- Bundle Identifier: `com.laravelboilerplate.laravel_boilerplate_mobile`
- Enable "Automatically manage signing"

**3.3 Configure Build Settings**
- Deployment Target: iOS 12.0 or higher
- Supported Architectures: arm64
- Build Configuration: Release

#### **Step 4: Build iOS App**

**4.1 Build Release Version**
```bash
# Clean previous builds
flutter clean
flutter pub get

# Build iOS release
flutter build ios --release

# This creates: build/ios/iphoneos/Runner.app
```

**4.2 Create Archive in Xcode**
- Open `ios/Runner.xcworkspace` in Xcode
- Select "Any iOS Device" as destination
- Product → Archive
- Wait for archive to complete

#### **Step 5: Upload to App Store Connect**

**5.1 Create App in App Store Connect**
- Go to [App Store Connect](https://appstoreconnect.apple.com)
- My Apps → + → New App
- Fill in app information:
  - Platform: iOS
  - Name: Laravel Boilerplate Mobile
  - Bundle ID: com.laravelboilerplate.laravel_boilerplate_mobile
  - SKU: unique identifier
  - User Access: Full Access

**5.2 Upload Archive**
- In Xcode Organizer (after archiving)
- Select your archive
- Click "Distribute App"
- Choose "App Store Connect"
- Follow the upload process

**5.3 Complete App Information**
```
Required Information:
- App Information: Name, Bundle ID, SKU
- Pricing and Availability: Free or paid, territories
- App Store Information: Description, keywords, support URL
- Build: Select uploaded build
- App Review Information: Contact info, demo account
- Version Release: Manual or automatic release

Screenshots Required:
- iPhone 6.7": 1284x2778 (3 required)
- iPhone 6.5": 1242x2688 (3 required)
- iPhone 5.5": 1242x2208 (3 required)
- iPad Pro 12.9": 2048x2732 (3 required)
- iPad Pro 11": 1668x2388 (3 required)
```

**5.4 Submit for Review**
- Complete all required sections
- Click "Submit for Review"
- Wait for Apple's review (1-7 days typically)

---

### **🔧 Deployment Automation (CI/CD)**

#### **GitHub Actions for Automated Deployment**

```yaml
# .github/workflows/deploy.yml
name: Deploy to Stores

on:
  push:
    tags:
      - 'v*'

jobs:
  deploy-android:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3

      - name: Setup Java
        uses: actions/setup-java@v3
        with:
          distribution: 'zulu'
          java-version: '11'

      - name: Setup Flutter
        uses: subosito/flutter-action@v2
        with:
          flutter-version: '3.16.0'

      - name: Get dependencies
        run: flutter pub get

      - name: Run tests
        run: flutter test

      - name: Build APK
        run: flutter build apk --release

      - name: Build App Bundle
        run: flutter build appbundle --release

      - name: Upload to Play Store
        uses: r0adkll/upload-google-play@v1
        with:
          serviceAccountJsonPlainText: ${{ secrets.SERVICE_ACCOUNT_JSON }}
          packageName: com.laravelboilerplate.laravel_boilerplate_mobile
          releaseFiles: build/app/outputs/bundle/release/app-release.aab
          track: production

  deploy-ios:
    runs-on: macos-latest
    steps:
      - uses: actions/checkout@v3

      - name: Setup Flutter
        uses: subosito/flutter-action@v2
        with:
          flutter-version: '3.16.0'

      - name: Get dependencies
        run: flutter pub get

      - name: Build iOS
        run: flutter build ios --release --no-codesign

      - name: Build and upload to App Store
        uses: apple-actions/import-codesign-certs@v1
        with:
          p12-file-base64: ${{ secrets.IOS_P12_BASE64 }}
          p12-password: ${{ secrets.IOS_P12_PASSWORD }}

      - name: Upload to App Store
        run: |
          xcrun altool --upload-app \
            --type ios \
            --file build/ios/iphoneos/Runner.app \
            --username ${{ secrets.APPLE_ID }} \
            --password ${{ secrets.APPLE_APP_PASSWORD }}
```

---

### **📋 Pre-Deployment Checklist**

#### **General**
- [ ] App tested on multiple devices and screen sizes
- [ ] All features working correctly
- [ ] Performance optimized (no memory leaks, smooth animations)
- [ ] Proper error handling implemented
- [ ] Privacy policy created and linked
- [ ] Terms of service created (if needed)
- [ ] App icons created for all required sizes
- [ ] Screenshots taken for all required device types
- [ ] App description written and optimized for ASO

#### **Android Specific**
- [ ] Keystore file backed up securely
- [ ] App bundle built and tested
- [ ] Permissions properly declared in AndroidManifest.xml
- [ ] ProGuard rules configured (if using obfuscation)
- [ ] Target SDK version updated to latest
- [ ] Google Play Console account set up
- [ ] Content rating completed
- [ ] Store listing information completed

#### **iOS Specific**
- [ ] Apple Developer account active
- [ ] App ID created and configured
- [ ] Certificates and provisioning profiles set up
- [ ] Info.plist permissions properly described
- [ ] App Store Connect app created
- [ ] Build uploaded and processed
- [ ] App Store review guidelines compliance checked
- [ ] TestFlight testing completed (recommended)

---

## 🎓 **Learning Resources**

### **Official Documentation**
- [Flutter Documentation](https://docs.flutter.dev/)
- [Dart Language Tour](https://dart.dev/guides/language/language-tour)
- [Flutter Widget Catalog](https://docs.flutter.dev/development/ui/widgets)

### **Video Tutorials**
- [Flutter Official YouTube Channel](https://www.youtube.com/c/flutterdev)
- [The Net Ninja Flutter Tutorial](https://www.youtube.com/playlist?list=PL4cUxeGkcC9jLYyp2Aoh6hcWuxFDX6PBJ)
- [Academind Flutter Course](https://www.youtube.com/watch?v=x0uinJvhNxI)

### **Practice Projects**
1. **Todo App**: Basic CRUD operations
2. **Weather App**: API integration
3. **Chat App**: Real-time features
4. **E-commerce App**: Complex state management
5. **Social Media App**: Advanced features

### **Community**
- [Flutter Community on Discord](https://discord.gg/flutter)
- [r/FlutterDev on Reddit](https://www.reddit.com/r/FlutterDev/)
- [Flutter Community on GitHub](https://github.com/fluttercommunity)

---

## 🎯 **Next Steps for Your Project**

Now that you understand Flutter fundamentals, here's what to focus on for your Laravel Boilerplate Mobile app:

1. **Complete Phase 1**: Finish dependency injection and HTTP client setup
2. **Implement Authentication**: Login, register, token management
3. **Build Core UI**: Design system, navigation, basic screens
4. **Add User Management**: Profile, user listing, CRUD operations
5. **Implement Messaging**: Real-time chat with your Laravel backend
6. **Add Notifications**: Push notifications and in-app notifications
7. **Testing**: Write comprehensive tests for all features
8. **Deployment**: Follow the deployment guide above

**Remember**: Start small, test frequently, and iterate based on feedback!

---

**Happy Flutter Development! 🚀**
