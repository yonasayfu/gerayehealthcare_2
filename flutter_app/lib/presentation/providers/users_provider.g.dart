// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'users_provider.dart';

// **************************************************************************
// RiverpodGenerator
// **************************************************************************

String _$usersHash() => r'827835f750ae3e2985718ba7a018224220868f2c';

/// See also [Users].
@ProviderFor(Users)
final usersProvider = AutoDisposeNotifierProvider<Users, UsersState>.internal(
  Users.new,
  name: r'usersProvider',
  debugGetCreateSourceHash:
      const bool.fromEnvironment('dart.vm.product') ? null : _$usersHash,
  dependencies: null,
  allTransitiveDependencies: null,
);

typedef _$Users = AutoDisposeNotifier<UsersState>;
String _$userDetailHash() => r'a89da7440d0e9c8bc41d5a25be6f459c56c782f8';

/// Copied from Dart SDK
class _SystemHash {
  _SystemHash._();

  static int combine(int hash, int value) {
    // ignore: parameter_assignments
    hash = 0x1fffffff & (hash + value);
    // ignore: parameter_assignments
    hash = 0x1fffffff & (hash + ((0x0007ffff & hash) << 10));
    return hash ^ (hash >> 6);
  }

  static int finish(int hash) {
    // ignore: parameter_assignments
    hash = 0x1fffffff & (hash + ((0x03ffffff & hash) << 3));
    // ignore: parameter_assignments
    hash = hash ^ (hash >> 11);
    return 0x1fffffff & (hash + ((0x00003fff & hash) << 15));
  }
}

abstract class _$UserDetail
    extends BuildlessAutoDisposeNotifier<AsyncValue<User?>> {
  late final int userId;

  AsyncValue<User?> build(
    int userId,
  );
}

/// See also [UserDetail].
@ProviderFor(UserDetail)
const userDetailProvider = UserDetailFamily();

/// See also [UserDetail].
class UserDetailFamily extends Family<AsyncValue<User?>> {
  /// See also [UserDetail].
  const UserDetailFamily();

  /// See also [UserDetail].
  UserDetailProvider call(
    int userId,
  ) {
    return UserDetailProvider(
      userId,
    );
  }

  @override
  UserDetailProvider getProviderOverride(
    covariant UserDetailProvider provider,
  ) {
    return call(
      provider.userId,
    );
  }

  static const Iterable<ProviderOrFamily>? _dependencies = null;

  @override
  Iterable<ProviderOrFamily>? get dependencies => _dependencies;

  static const Iterable<ProviderOrFamily>? _allTransitiveDependencies = null;

  @override
  Iterable<ProviderOrFamily>? get allTransitiveDependencies =>
      _allTransitiveDependencies;

  @override
  String? get name => r'userDetailProvider';
}

/// See also [UserDetail].
class UserDetailProvider
    extends AutoDisposeNotifierProviderImpl<UserDetail, AsyncValue<User?>> {
  /// See also [UserDetail].
  UserDetailProvider(
    int userId,
  ) : this._internal(
          () => UserDetail()..userId = userId,
          from: userDetailProvider,
          name: r'userDetailProvider',
          debugGetCreateSourceHash:
              const bool.fromEnvironment('dart.vm.product')
                  ? null
                  : _$userDetailHash,
          dependencies: UserDetailFamily._dependencies,
          allTransitiveDependencies:
              UserDetailFamily._allTransitiveDependencies,
          userId: userId,
        );

  UserDetailProvider._internal(
    super._createNotifier, {
    required super.name,
    required super.dependencies,
    required super.allTransitiveDependencies,
    required super.debugGetCreateSourceHash,
    required super.from,
    required this.userId,
  }) : super.internal();

  final int userId;

  @override
  AsyncValue<User?> runNotifierBuild(
    covariant UserDetail notifier,
  ) {
    return notifier.build(
      userId,
    );
  }

  @override
  Override overrideWith(UserDetail Function() create) {
    return ProviderOverride(
      origin: this,
      override: UserDetailProvider._internal(
        () => create()..userId = userId,
        from: from,
        name: null,
        dependencies: null,
        allTransitiveDependencies: null,
        debugGetCreateSourceHash: null,
        userId: userId,
      ),
    );
  }

  @override
  AutoDisposeNotifierProviderElement<UserDetail, AsyncValue<User?>>
      createElement() {
    return _UserDetailProviderElement(this);
  }

  @override
  bool operator ==(Object other) {
    return other is UserDetailProvider && other.userId == userId;
  }

  @override
  int get hashCode {
    var hash = _SystemHash.combine(0, runtimeType.hashCode);
    hash = _SystemHash.combine(hash, userId.hashCode);

    return _SystemHash.finish(hash);
  }
}

mixin UserDetailRef on AutoDisposeNotifierProviderRef<AsyncValue<User?>> {
  /// The parameter `userId` of this provider.
  int get userId;
}

class _UserDetailProviderElement
    extends AutoDisposeNotifierProviderElement<UserDetail, AsyncValue<User?>>
    with UserDetailRef {
  _UserDetailProviderElement(super.provider);

  @override
  int get userId => (origin as UserDetailProvider).userId;
}
// ignore_for_file: type=lint
// ignore_for_file: subtype_of_sealed_class, invalid_use_of_internal_member, invalid_use_of_visible_for_testing_member
