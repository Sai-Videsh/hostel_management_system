# 🚪 Check-in/Check-out Feature Documentation

## ✅ Feature Overview

The Check-in/Check-out system allows administrators to track student entry and exit from the hostel in real-time.

---

## 📋 Features Implemented

### 1. **Entry Recording**
- ✅ Select student from dropdown (shows Reg No, Name, Room)
- ✅ Choose action type (Check-in or Check-out)
- ✅ Auto-filled date (today's date)
- ✅ Auto-filled time (current system time)
- ✅ Optional remarks field
- ✅ Automatic student details population

### 2. **Real-time Statistics Dashboard**
- 📊 Today's Check-ins count
- 📊 Today's Check-outs count
- 📊 Currently In Hostel count
- 📊 Currently Out count

### 3. **Records Management**
- 📝 View all check-in/check-out records
- 🔍 Filter by action type (check-in/check-out)
- 🔍 Filter by date
- 🔍 Search by registration number or name
- 🗑️ Delete records (with confirmation)
- 📄 Shows last 50 records

### 4. **Smart Features**
- ⏰ Auto-updates time every minute
- 🎨 Color-coded badges (Green for check-in, Yellow for check-out)
- 📱 Responsive design
- ✨ Clean and intuitive UI

---

## 🗄️ Database Structure

### Table: `hostel_checkin_checkout`

| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment ID |
| reg_no | VARCHAR(255) | Student registration number |
| student_name | VARCHAR(255) | Student full name |
| room_no | INT | Room number (nullable) |
| action_type | ENUM | 'check-in' or 'check-out' |
| action_date | DATE | Date of action |
| action_time | TIME | Time of action |
| remarks | TEXT | Optional notes |
| recorded_by | VARCHAR(255) | Admin who recorded |
| created_at | TIMESTAMP | Record creation timestamp |

### View: `checkin_checkout_view`
Combines check-in/check-out data with student registration details for easy reporting.

---

## 📂 Files Created

1. **`checkin_checkout_table.sql`** - Database schema
2. **`checkinManage.php`** - Main UI page
3. **`partials/_checkinManage.php`** - Backend handler
4. **`partials/_nav.php`** - Updated navigation (added new tab)

---

## 🚀 Installation Steps

### Step 1: Import Database Table
```sql
-- Run this in phpMyAdmin or MySQL
SOURCE checkin_checkout_table.sql;
```

OR manually execute the SQL file in phpMyAdmin:
1. Open phpMyAdmin
2. Select `hostel_db` database
3. Go to "Import" tab
4. Choose `checkin_checkout_table.sql`
5. Click "Go"

### Step 2: Verify Navigation
The "Check-in/Check-out" tab should now appear in the admin sidebar menu.

### Step 3: Test the Feature
1. Login as admin
2. Click "Check-in/Check-out" in sidebar
3. Select a student
4. Choose action type
5. Add optional remarks
6. Click "Record Entry"

---

## 💡 How to Use

### Recording Check-in (Student Entering Hostel)
1. Go to **Check-in/Check-out** page
2. Select student from dropdown
3. Choose **"✅ Check-in (Entry)"**
4. Date and time auto-filled (can be modified)
5. Add remarks if needed (e.g., "Returning from home")
6. Click **"Record Entry"**

### Recording Check-out (Student Leaving Hostel)
1. Go to **Check-in/Check-out** page
2. Select student from dropdown
3. Choose **"🚪 Check-out (Exit)"**
4. Date and time auto-filled (can be modified)
5. Add remarks if needed (e.g., "Going home for weekend")
6. Click **"Record Entry"**

### Viewing Statistics
The dashboard shows:
- **Today's Check-ins**: Students who entered today
- **Today's Check-outs**: Students who left today
- **Currently In Hostel**: Students whose last action was check-in
- **Currently Out**: Students whose last action was check-out

### Filtering Records
- **By Action**: Select "Check-in Only" or "Check-out Only"
- **By Date**: Pick a specific date
- **By Student**: Type registration number or name
- **Clear Filters**: Click "Clear Filters" button

### Deleting Records
1. Find the record in the table
2. Click the red trash icon
3. Confirm deletion

---

## 🎨 UI Features

### Color Coding
- 🟢 **Green Badge**: Check-in entries
- 🟡 **Yellow Badge**: Check-out entries
- 🔵 **Blue Card**: Currently in hostel
- 🔴 **Red Card**: Currently out

### Auto-fill Behavior
When you select a student:
- Student name auto-fills (hidden field)
- Room number auto-fills (hidden field)
- Current date auto-fills
- Current time auto-fills

### Time Auto-update
The time field automatically updates every minute to show current time.

---

## 📊 Use Cases

### 1. **Security Monitoring**
Track who is currently in the hostel for safety purposes.

### 2. **Attendance Tracking**
Monitor student presence patterns.

### 3. **Emergency Situations**
Quickly identify who is in the hostel during emergencies.

### 4. **Leave Management**
Track when students leave and return (with remarks).

### 5. **Reporting**
Generate reports on student movement patterns.

---

## 🔧 Customization Options

### Change Time Format
In `checkinManage.php`, line with `date('h:i A')`:
- 24-hour format: Change to `date('H:i')`
- 12-hour format: Keep as `date('h:i A')`

### Change Date Format
In `checkinManage.php`, line with `date('d-M-Y')`:
- US format: Change to `date('m/d/Y')`
- ISO format: Change to `date('Y-m-d')`

### Modify Statistics Calculation
Edit SQL queries in the statistics cards section to change logic.

### Add More Filters
Add new filter dropdowns in the "Filter Options" section.

---

## 🐛 Troubleshooting

### Issue: "Table doesn't exist"
**Solution**: Import `checkin_checkout_table.sql` in phpMyAdmin

### Issue: "No students in dropdown"
**Solution**: Ensure students are registered in `userregistration` table

### Issue: "Time not updating"
**Solution**: Check JavaScript console for errors

### Issue: "Can't delete records"
**Solution**: Check admin session and database permissions

---

## 🔐 Security Features

- ✅ Admin authentication required
- ✅ SQL injection prevention (mysqli_real_escape_string)
- ✅ Session-based access control
- ✅ Confirmation before deletion

---

## 📈 Future Enhancements (Optional)

1. **Export to Excel**: Add export functionality for reports
2. **SMS/Email Alerts**: Notify parents when student leaves
3. **QR Code Scanning**: Quick check-in using student ID cards
4. **Biometric Integration**: Fingerprint-based entry/exit
5. **Mobile App**: Allow students to self-check-in
6. **Analytics Dashboard**: Graphs and charts for patterns
7. **Late Entry Alerts**: Flag students entering after curfew

---

## 📞 Support

If you encounter any issues:
1. Check database connection in `_dbconnect.php`
2. Verify table exists: `SHOW TABLES LIKE 'hostel_checkin_checkout';`
3. Check Apache error logs: `C:\xampp\apache\logs\error.log`
4. Ensure admin is logged in

---

## ✨ Summary

The Check-in/Check-out feature is now fully integrated into your hostel management system. It provides:
- Real-time tracking of student movements
- Easy-to-use interface for admins
- Comprehensive statistics dashboard
- Powerful filtering and search capabilities
- Secure and reliable data storage

**Access it at**: `http://localhost/hostel-management-system/index.php?page=checkinManage`

---

**Developed for IIITDM Kurnool Hostel Management System**
