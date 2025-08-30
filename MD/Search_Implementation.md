
# Global Search Implementation Guide: Geraye Health Care

This document provides a guide to the global search implementation in the Geraye Health Care project.

## Overview

The global search system provides a fast, intelligent, and comprehensive search experience across all major healthcare entities. It is designed with a professional user experience and performance optimization in mind.

## Searchable Entities

The global search system allows users to search across a wide range of entities, including:

*   **Healthcare Core:** Patients, Staff Members, Visit Services, Caregiver Assignments, Referrals
*   **Financial & Billing:** Invoices, Insurance Claims, Insurance Policies
*   **Inventory & Supplies:** Inventory Items, Suppliers
*   **Services & Events:** Services, Events
*   **Marketing & Leads:** Marketing Leads, Marketing Campaigns

## Features

### Performance Optimizations

*   **Caching:** Search results are cached for 5 minutes to improve performance.
*   **Query Optimization:** Database queries are optimized using `select()` to limit the number of columns returned.
*   **Result Limiting:** The number of results per entity is limited to ensure a fast response time.
*   **Eager Loading:** Relationships are eager-loaded to prevent N+1 query issues.
*   **Database Indexes:** Database indexes are used to speed up search queries.
*   **Debounced Input:** A 300ms delay is used to prevent excessive API calls.

### Relevance Scoring

Results are automatically ranked by relevance using a scoring system that prioritizes exact matches and healthcare-related entities.

### User Experience

*   **Modal Interface:** The search interface is displayed in a modal window that can be triggered by a button click or a keyboard shortcut (`Cmd+K` or `Ctrl+K`).
*   **Responsive Design:** The search interface is fully responsive and works on both desktop and mobile devices.
*   **Search States:** The search interface displays different states for empty, loading, and no results.
*   **Keyboard Shortcuts:** Keyboard shortcuts are available for opening and closing the search modal.

## Technical Implementation

### Backend

*   **Service Class:** The `GlobalSearchService` class contains the business logic for searching across different entities.
*   **Caching Layer:** Redis is used for caching search results.
*   **Database:** PostgreSQL is used as the database, with optimized queries and proper indexing.

### Frontend

*   **Modal Component:** A Vue 3 component is used to display the search interface.
*   **State Management:** The search state is managed using reactive variables.
*   **UI Framework:** Tailwind CSS is used for styling the search interface.

This document provides a high-level overview of the global search implementation. For more detailed information, please refer to the source code and other documentation in this repository.
