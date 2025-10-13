import express from "express";
import multer from "multer";
import path from "path";
import mysql from "mysql2/promise";

const router = express.Router();
const upload = multer({ dest: "uploads/" });

// DB connection
const db = await mysql.createConnection({
  host: "localhost",
  user: "root",
  password: "",
  database: "web3_jobs"
});

// Handle profile save
router.post("/profile", upload.fields([{ name: "profilePic" }, { name: "cvFile" }]), async (req, res) => {
  try {
    const { fullName, email, phone, education, skills } = req.body;
    const profilePic = req.files.profilePic ? req.files.profilePic[0].filename : null;
    const cvFile = req.files.cvFile ? req.files.cvFile[0].filename : null;

    await db.execute(
      "INSERT INTO student_profiles (fullName, email, phone, education, skills, profilePic, cvFile) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE phone=?, education=?, skills=?, profilePic=?, cvFile=?",
      [fullName, email, phone, education, skills, profilePic, cvFile, phone, education, skills, profilePic, cvFile]
    );

    res.json({ message: "Profile saved successfully" });
  } catch (err) {
    console.error(err);
    res.status(500).json({ message: "Error saving profile" });
  }
});

export default router;
