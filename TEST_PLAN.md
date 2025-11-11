# E-Commerce Project - Test Plan

## Overview
This test plan outlines the testing strategy for all implemented features in the e-commerce project. The tests are organized by feature area and include both functional and integration tests.

## Testing Prerequisites
- Ensure MySQL server is running
- Run database migrations: `php artisan migrate`
- Seed database with test data: `php artisan db:seed`
- For API tests, ensure application is running: `php artisan serve`

## Test Categories

### 1. Authentication Tests

#### 1.1 User Registration
- Test successful user registration with valid data
- Test registration failure with invalid data (validation)
- Test registration with duplicate email
- Test that newly registered user is not admin

#### 1.2 User Login
- Test successful login with correct credentials
- Test login failure with incorrect credentials
- Test login with non-existent user
- Test redirect after login

#### 1.3 User Logout
- Test successful logout
- Test that user is redirected after logout
- Test that user session is cleared

#### 1.4 Password Reset
- Test password reset request form
- Test password reset link generation
- Test password reset with valid token
- Test password reset with invalid token

#### 1.5 Profile Management
- Test profile view access
- Test profile update with valid data
- Test profile update with invalid data
- Test email change and verification
- Test account deletion

### 2. Product Management Tests

#### 2.1 Product Display (Frontend)
- Test homepage with featured products
- Test product listing page
- Test product search functionality
- Test product filtering by category
- Test product filtering by price range
- Test product sorting options
- Test pagination on product listing

#### 2.2 Product Detail Page
- Test product detail page with valid product
- Test product detail page with inactive product (should 404)
- Test related products display
- Test product image gallery display
- Test "Add to Cart" functionality on product page

#### 2.3 Admin Product Management
- Test access restriction (non-admin users cannot access)
- Test product listing page for admin
- Test product creation form access
- Test product creation with valid data
- Test product creation with invalid data
- Test product editing form access
- Test product editing with valid data
- Test product editing with invalid data
- Test product soft deletion
- Test product restoration from trash
- Test permanent deletion (force delete)
- Test primary image setting functionality

### 3. Shopping Cart Tests

#### 3.1 Cart Operations
- Test adding product to cart
- Test adding same product multiple times
- Test updating cart item quantity
- Test removing item from cart
- Test clearing entire cart
- Test cart persistence across sessions

#### 3.2 Cart Validation
- Test adding out-of-stock product to cart
- Test updating quantity beyond available stock
- Test cart item removal when product is deleted
- Test cart cleanup for invalid products

#### 3.3 Cart Totals Calculation
- Test subtotal calculation
- Test tax calculation (10%)
- Test shipping cost calculation (free over $50)
- Test total calculation

### 4. Checkout Process Tests

#### 4.1 Checkout Access
- Test checkout page access (authenticated users only)
- Test redirect when cart is empty
- Test checkout page with valid cart contents

#### 4.2 Order Creation
- Test successful order creation with valid data
- Test order creation with invalid form data
- Test order number generation
- Test order status initialization
- Test order total calculation accuracy
- Test order items creation
- Test cart clearing after successful order

#### 4.3 Shipping Information
- Test shipping address formatting
- Test customer notes inclusion
- Test validation for required shipping fields

### 5. Order Management Tests

#### 5.1 User Order Management
- Test order listing access (authenticated users only)
- Test individual order view
- Test access restriction (users can only view their own orders)

#### 5.2 Admin Order Management
- Test admin order listing
- Test admin order detail view
- Test order status updates
- Test order status validation

### 6. Category Management Tests

#### 6.1 Category Display
- Test category listing on frontend
- Test category filtering functionality
- Test category detail page
- Test products by category

#### 6.2 Admin Category Management
- Test category creation with valid data
- Test category creation with invalid data
- Test category editing
- Test category deletion
- Test products remain accessible after category deletion (due to cascade)

### 7. Multi-Image Support Tests

#### 7.1 Image Management
- Test multiple image uploads for products
- Test primary image selection
- Test image order sorting
- Test image display in galleries
- Test main image URL generation

#### 7.2 Image Relationships
- Test proper image relationships with products
- Test image cleanup when product is deleted
- Test primary image access

### 8. Performance Tests

#### 8.1 Database Performance
- Test query optimization with eager loading
- Test pagination performance
- Test search functionality performance

#### 8.2 Application Performance
- Test page load times
- Test concurrent user sessions
- Test cart session handling

### 9. Security Tests

#### 9.1 Access Control
- Test authentication requirements for protected routes
- Test admin role requirements
- Test route model binding security
- Test unauthorized access attempts

#### 9.2 Input Validation
- Test all form submissions with malicious data
- Test SQL injection attempts
- Test XSS attempts
- Test file upload validation

### 10. Integration Tests

#### 10.1 Complete Purchase Flow
- Test complete flow: browse → add to cart → checkout → order confirmation
- Test edge cases in the purchase flow
- Test cart sharing/privacy between users

#### 10.2 Admin Workflow
- Test complete admin workflow: login → manage products → process orders
- Test administrative tasks in sequence

## Test Implementation Plan

### Unit Tests
- Product model methods and accessors
- Category model methods
- Order model methods
- Cart helper functions
- Form request validation rules

### Feature Tests
- HTTP requests for all controller methods
- Authentication flows
- Form submissions and validations
- Database interactions
- Session management

### API Tests (if applicable)
- REST API endpoints (if implemented)
- JSON response validation
- API authentication

## Testing Tools and Frameworks
- PHPUnit for unit and feature tests
- Laravel Dusk for browser testing (if configured)
- Laravel Testing Tools (assertions, factories, etc.)
- Mocking and stubbing for external dependencies

## Environment Setup
- Use testing database (sqlite in-memory)
- Database transactions for test isolation
- Test user accounts and data factories
- Mock services (mail, external APIs)

## Expected Outcomes
- All tests should pass with 100% code coverage for critical paths
- Performance benchmarks met
- Security vulnerabilities identified and addressed
- Error handling properly implemented
- User experience validated

## Bug Reporting
- Document any failing tests with detailed steps
- Include expected vs actual results
- Categorize by severity (critical, high, medium, low)
- Track in project issue management system

## Maintenance
- Update tests when features are modified
- Add new tests for new features
- Regular test suite execution in CI/CD pipeline
- Monitor test performance and stability