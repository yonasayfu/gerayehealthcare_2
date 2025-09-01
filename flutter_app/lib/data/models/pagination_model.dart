import 'package:json_annotation/json_annotation.dart';

part 'pagination_model.g.dart';

@JsonSerializable(genericArgumentFactories: true)
class PaginationModel<T> {
  @JsonKey(name: 'current_page')
  final int currentPage;
  
  @JsonKey(name: 'last_page')
  final int lastPage;
  
  @JsonKey(name: 'per_page')
  final int perPage;
  
  final int total;
  
  @JsonKey(name: 'has_more')
  final bool hasMore;
  
  final List<T> data;

  const PaginationModel({
    required this.currentPage,
    required this.lastPage,
    required this.perPage,
    required this.total,
    required this.hasMore,
    required this.data,
  });

  factory PaginationModel.fromJson(
    Map<String, dynamic> json,
    T Function(Object? json) fromJsonT,
  ) =>
      _$PaginationModelFromJson(json, fromJsonT);

  Map<String, dynamic> toJson(Object Function(T value) toJsonT) =>
      _$PaginationModelToJson(this, toJsonT);

  // Helper methods
  bool get isFirstPage => currentPage == 1;
  bool get isLastPage => currentPage == lastPage;
  bool get isEmpty => data.isEmpty;
  bool get isNotEmpty => data.isNotEmpty;
  int get itemCount => data.length;
  
  // Calculate total pages
  int get totalPages => lastPage;
  
  // Check if there are more pages
  bool get hasNextPage => hasMore;
  bool get hasPreviousPage => currentPage > 1;
  
  // Get next/previous page numbers
  int? get nextPage => hasNextPage ? currentPage + 1 : null;
  int? get previousPage => hasPreviousPage ? currentPage - 1 : null;
  
  // Calculate item range for current page
  int get startItem => ((currentPage - 1) * perPage) + 1;
  int get endItem => currentPage * perPage > total ? total : currentPage * perPage;
  
  // Create empty pagination
  factory PaginationModel.empty() {
    return PaginationModel<T>(
      currentPage: 1,
      lastPage: 1,
      perPage: 20,
      total: 0,
      hasMore: false,
      data: [],
    );
  }

  // Create pagination with single page
  factory PaginationModel.single(List<T> items) {
    return PaginationModel<T>(
      currentPage: 1,
      lastPage: 1,
      perPage: items.length,
      total: items.length,
      hasMore: false,
      data: items,
    );
  }

  // Copy with new data (useful for appending pages)
  PaginationModel<T> copyWith({
    int? currentPage,
    int? lastPage,
    int? perPage,
    int? total,
    bool? hasMore,
    List<T>? data,
  }) {
    return PaginationModel<T>(
      currentPage: currentPage ?? this.currentPage,
      lastPage: lastPage ?? this.lastPage,
      perPage: perPage ?? this.perPage,
      total: total ?? this.total,
      hasMore: hasMore ?? this.hasMore,
      data: data ?? this.data,
    );
  }

  // Append new page data
  PaginationModel<T> appendPage(PaginationModel<T> newPage) {
    return copyWith(
      currentPage: newPage.currentPage,
      lastPage: newPage.lastPage,
      hasMore: newPage.hasMore,
      data: [...data, ...newPage.data],
    );
  }

  @override
  String toString() {
    return 'PaginationModel{currentPage: $currentPage, lastPage: $lastPage, perPage: $perPage, total: $total, hasMore: $hasMore, itemCount: ${data.length}}';
  }

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is PaginationModel &&
          runtimeType == other.runtimeType &&
          currentPage == other.currentPage &&
          lastPage == other.lastPage &&
          perPage == other.perPage &&
          total == other.total &&
          hasMore == other.hasMore &&
          data == other.data;

  @override
  int get hashCode =>
      currentPage.hashCode ^
      lastPage.hashCode ^
      perPage.hashCode ^
      total.hashCode ^
      hasMore.hashCode ^
      data.hashCode;
}
