// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'sync_provider.dart';

// **************************************************************************
// RiverpodGenerator
// **************************************************************************

String _$syncHash() => r'6d38a74e22fd9e5a6bf85d870c7fd4ed94c90040';

/// See also [Sync].
@ProviderFor(Sync)
final syncProvider = AutoDisposeNotifierProvider<Sync, SyncState>.internal(
  Sync.new,
  name: r'syncProvider',
  debugGetCreateSourceHash:
      const bool.fromEnvironment('dart.vm.product') ? null : _$syncHash,
  dependencies: null,
  allTransitiveDependencies: null,
);

typedef _$Sync = AutoDisposeNotifier<SyncState>;
String _$connectivityHash() => r'5f08ab8300b671d6c3e279be2738d69ded4f2810';

/// See also [Connectivity].
@ProviderFor(Connectivity)
final connectivityProvider =
    AutoDisposeNotifierProvider<Connectivity, bool>.internal(
  Connectivity.new,
  name: r'connectivityProvider',
  debugGetCreateSourceHash:
      const bool.fromEnvironment('dart.vm.product') ? null : _$connectivityHash,
  dependencies: null,
  allTransitiveDependencies: null,
);

typedef _$Connectivity = AutoDisposeNotifier<bool>;
String _$offlineQueueHash() => r'1e28f20561e07f93970a2eb82eab9e8cf79f6361';

/// See also [OfflineQueue].
@ProviderFor(OfflineQueue)
final offlineQueueProvider = AutoDisposeNotifierProvider<OfflineQueue,
    List<Map<String, dynamic>>>.internal(
  OfflineQueue.new,
  name: r'offlineQueueProvider',
  debugGetCreateSourceHash:
      const bool.fromEnvironment('dart.vm.product') ? null : _$offlineQueueHash,
  dependencies: null,
  allTransitiveDependencies: null,
);

typedef _$OfflineQueue = AutoDisposeNotifier<List<Map<String, dynamic>>>;
String _$dataIntegrityHash() => r'b67fab98c38b93d03874f02f831e331e9fc5d7c5';

/// See also [DataIntegrity].
@ProviderFor(DataIntegrity)
final dataIntegrityProvider =
    AutoDisposeNotifierProvider<DataIntegrity, bool>.internal(
  DataIntegrity.new,
  name: r'dataIntegrityProvider',
  debugGetCreateSourceHash: const bool.fromEnvironment('dart.vm.product')
      ? null
      : _$dataIntegrityHash,
  dependencies: null,
  allTransitiveDependencies: null,
);

typedef _$DataIntegrity = AutoDisposeNotifier<bool>;
// ignore_for_file: type=lint
// ignore_for_file: subtype_of_sealed_class, invalid_use_of_internal_member, invalid_use_of_visible_for_testing_member
