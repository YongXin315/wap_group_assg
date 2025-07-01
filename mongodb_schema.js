// MongoDB Schema Reference for wap_system

// 1. Students Collection
// Schema Example:
// {
//   _id: ObjectId,
//   student_id: String, // unique
//   student_email: String, // unique
//   student_name: String,
//   password: String
// }
db.createCollection("students", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["student_id", "student_email", "student_name", "password"],
      properties: {
        student_id: { bsonType: "string", description: "Student ID - unique" },
        student_email: { bsonType: "string", description: "Student email - unique" },
        student_name: { bsonType: "string", description: "Student name" },
        password: { bsonType: "string", description: "Hashed password" }
      }
    }
  }
});
db.students.createIndex({ student_id: 1 }, { unique: true });
db.students.createIndex({ student_email: 1 }, { unique: true });

// 2. Admins Collection
// Schema Example:
// {
//   _id: String, // e.g. "ADM00001"
//   admin_email: String, // unique
//   password: String
// }
db.createCollection("admins", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["_id", "admin_email", "password"],
      properties: {
        _id: { bsonType: "string", description: "Admin ID - unique" },
        admin_email: { bsonType: "string", description: "Admin email - unique" },
        password: { bsonType: "string", description: "Hashed password" }
      }
    }
  }
});
db.admins.createIndex({ _id: 1 }, { unique: true });
db.admins.createIndex({ admin_email: 1 }, { unique: true });

// 3. Rooms Collection
// Schema Example:
// {
//   _id: ObjectId,
//   room_name: String, // unique
//   type: String,
//   block: String,
//   floor: String,
//   amenities: String,
//   min_occupancy: Int,
//   max_occupancy: Int,
//   status: String
// }
db.createCollection("rooms", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["room_name", "type", "block", "floor", "amenities", "min_occupancy", "max_occupancy", "status"],
      properties: {
        room_name: { bsonType: "string", description: "Room name - unique" },
        type: { bsonType: "string", description: "Room type" },
        block: { bsonType: "string", description: "Block" },
        floor: { bsonType: "string", description: "Floor" },
        amenities: { bsonType: "string", description: "Amenities" },
        min_occupancy: { bsonType: "int", description: "Minimum occupancy" },
        max_occupancy: { bsonType: "int", description: "Maximum occupancy" },
        status: { bsonType: "string", description: "Room status" }
      }
    }
  }
});
db.rooms.createIndex({ room_name: 1 }, { unique: true });

// 4. Bookings Collection
// Schema Example:
// {
//   _id: ObjectId,
//   student_id: String,
//   full_name: String,
//   room_id: String,
//   booking_date: String (YYYY-MM-DD),
//   start_time: String (HH:MM),
//   end_time: String (HH:MM),
//   purpose: String,
//   num_people: Int,
//   created_at: Date,
//   status: String,
//   day_of_week: String
// }
db.createCollection("bookings", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["student_id", "full_name", "room_id", "booking_date", "start_time", "end_time", "purpose", "num_people", "created_at", "status", "day_of_week"],
      properties: {
        student_id: { bsonType: "string", description: "Student ID" },
        full_name: { bsonType: "string", description: "Full name" },
        room_id: { bsonType: "string", description: "Room ID" },
        booking_date: { bsonType: "string", description: "Booking date (YYYY-MM-DD)" },
        start_time: { bsonType: "string", description: "Start time (HH:MM)" },
        end_time: { bsonType: "string", description: "End time (HH:MM)" },
        purpose: { bsonType: "string", description: "Purpose" },
        num_people: { bsonType: "int", description: "Number of people" },
        created_at: { bsonType: "date", description: "Created at" },
        status: { bsonType: "string", description: "Booking status" },
        day_of_week: { bsonType: "string", description: "Day of week" }
      }
    }
  }
});
db.bookings.createIndex({ student_id: 1 });
db.bookings.createIndex({ room_id: 1 });
db.bookings.createIndex({ booking_date: 1 });
db.bookings.createIndex({ status: 1 });

// 5. Class Timetable Collection
// Schema Example:
// {
//   _id: ObjectId,
//   room_id: String,
//   day_of_week: String,
//   start_time: String (HH:MM),
//   end_time: String (HH:MM)
// }
db.createCollection("class_timetable", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["room_id", "day_of_week", "start_time", "end_time"],
      properties: {
        room_id: { bsonType: "string", description: "Room ID" },
        day_of_week: { bsonType: "string", description: "Day of week" },
        start_time: { bsonType: "string", description: "Start time (HH:MM)" },
        end_time: { bsonType: "string", description: "End time (HH:MM)" }
      }
    }
  }
});
db.class_timetable.createIndex({ room_id: 1 });
db.class_timetable.createIndex({ day_of_week: 1 });