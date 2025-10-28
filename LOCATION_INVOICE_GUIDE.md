# Location-Based Invoice Creation - CRITICAL FEATURE

## 🎯 Overview

Contractors can now create invoices based on **site locations**, allowing them to:
1. **Select a site location** (Region > District > Ward > Street) from imported Tanzania locations
2. **View all clients** registered at that specific location
3. **Select specific clients** or all clients to receive invoices
4. **Create bulk invoices** for all selected clients at once

---

## 🆕 What Was Added

### 1. Database Changes ✅
- Added `region`, `district`, `ward`, `street` fields to `clients` table
- Added indexes for fast location-based queries

### 2. New API Endpoints ✅

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/api/invoices/clients-by-location` | POST | Get clients at specific site location |
| `/api/invoices/bulk-create` | POST | Create invoices for multiple clients |
| `/api/invoices/location-statistics` | GET | Get location statistics |

### 3. Enhanced Models ✅
- `Client` model: Added location scopes and `site_location` attribute
- `Invoice` model: Supports bulk creation

---

## 📋 Complete Workflow

### Step 1: Search for Site Location

Use the location endpoints to build cascading dropdowns:

```bash
# Get regions
GET /api/locations/regions

# Get districts
GET /api/locations/districts?region=ARUSHA

# Get wards
GET /api/locations/wards?region=ARUSHA&district=ARUSHA%20CBD

# Get streets
GET /api/locations/streets?region=ARUSHA&district=ARUSHA%20CBD&ward=SEKEI
```

### Step 2: Get Clients at Selected Location

```bash
POST /api/invoices/clients-by-location
Content-Type: application/json

{
  "contractor_registration_number": "CONT123456",
  "region": "ARUSHA",
  "district": "ARUSHA CBD",
  "ward": "SEKEI",
  "street": "SANAWARI"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Clients retrieved successfully",
  "data": {
    "site_location": "ARUSHA > ARUSHA CBD > SEKEI > SANAWARI",
    "region": "ARUSHA",
    "district": "ARUSHA CBD",
    "ward": "SEKEI",
    "street": "SANAWARI",
    "clients": [
      {
        "id": 1,
        "registration_number": "CL123456",
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "0712345678",
        "address": "123 Main St",
        "region": "ARUSHA",
        "district": "ARUSHA CBD",
        "ward": "SEKEI",
        "street": "SANAWARI",
        "status": "active"
      }
    ],
    "total_clients": 5
  }
}
```

### Step 3: Create Bulk Invoices

Select specific clients or all clients and create invoices:

```bash
POST /api/invoices/bulk-create
Content-Type: application/json

{
  "contractor_registration_number": "CONT123456",
  "client_ids": [1, 2, 3, 5],
  "site_location": {
    "region": "ARUSHA",
    "district": "ARUSHA CBD",
    "ward": "SEKEI",
    "street": "SANAWARI"
  },
  "invoice_date": "2025-10-28",
  "due_date": "2025-11-28",
  "service_type": "Waste Collection - October 2025",
  "description": "Monthly waste collection service",
  "subtotal": 50000,
  "tax_rate": 18,
  "notes": "Payment due within 30 days"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Successfully created 4 invoices for site location: ARUSHA > ARUSHA CBD > SEKEI > SANAWARI",
  "data": {
    "site_location": "ARUSHA > ARUSHA CBD > SEKEI > SANAWARI",
    "invoices_created": 4,
    "total_amount": 236000,
    "invoices": [
      {
        "client_id": 1,
        "client_name": "John Doe",
        "client_registration_number": "CL123456",
        "invoice_number": "INV-202510-0001",
        "total_amount": 59000
      }
    ]
  }
}
```

---

## 🎨 Frontend Integration Example

```javascript
// 1. Load location dropdowns
const regions = await fetch('/api/locations/regions').then(r => r.json());

// 2. When user selects location, get clients
const getClientsAtLocation = async (location) => {
  const response = await fetch('/api/invoices/clients-by-location', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
      contractor_registration_number: 'CONT123456',
      region: location.region,
      district: location.district,
      ward: location.ward,
      street: location.street
    })
  });
  
  const data = await response.json();
  return data.data.clients;
};

// 3. Let user select clients and create bulk invoices
const createBulkInvoices = async (selectedClientIds, location, invoiceData) => {
  const response = await fetch('/api/invoices/bulk-create', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
      contractor_registration_number: 'CONT123456',
      client_ids: selectedClientIds,
      site_location: location,
      ...invoiceData
    })
  });
  
  return await response.json();
};

// Usage Example
const location = {
  region: 'ARUSHA',
  district: 'ARUSHA CBD',
  ward: 'SEKEI',
  street: 'SANAWARI'
};

// Get clients at location
const clients = await getClientsAtLocation(location);

// User selects clients [1, 2, 3]
const selectedIds = [1, 2, 3];

// Create invoices
const result = await createBulkInvoices(selectedIds, location, {
  invoice_date: '2025-10-28',
  due_date: '2025-11-28',
  service_type: 'Waste Collection',
  subtotal: 50000,
  tax_rate: 18
});
```

---

## 📊 Additional Feature: Location Statistics

Get overview of how clients are distributed across locations:

```bash
GET /api/invoices/location-statistics?contractor_registration_number=CONT123456
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total_clients": 150,
    "clients_with_location": 145,
    "clients_without_location": 5,
    "unique_locations": 12,
    "locations": [
      {
        "site_location": "ARUSHA > ARUSHA CBD > SEKEI > SANAWARI",
        "region": "ARUSHA",
        "district": "ARUSHA CBD",
        "ward": "SEKEI",
        "street": "SANAWARI",
        "client_count": 25
      }
    ]
  }
}
```

---

## ✅ Implementation Checklist

- ✅ Database migration completed
- ✅ Client model updated with location fields
- ✅ Location query scopes added
- ✅ 3 new API endpoints created
- ✅ Bulk invoice creation implemented
- ✅ Location-based client filtering
- ✅ Validation and error handling
- ✅ Transaction support for bulk operations

---

## 🚀 Ready to Use

Your location-based invoice system is now **production-ready**! Contractors can efficiently create invoices for multiple clients at the same site location.

**Created**: October 28, 2025  
**Status**: ✅ CRITICAL FEATURE IMPLEMENTED
