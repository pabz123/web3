import express from "express";
import bcrypt from "bcryptjs";
import jwt from "jsonwebtoken";
import Student from "../models/Student.js";
import Employer from "../models/Employer.js";

const router = express.Router();

// ✅ Registration Route
router.post("/register", async (req, res) => {
  try {
    console.log("📩 Incoming registration data:", req.body);
    const {
      username,
      email,
      password,
      role,
      company_name,
      company_type,
      industry,
      company_description,
    } = req.body;

    // 🧠 Basic input validation
    if (!email || !password || !role) {
      return res.status(400).json({ error: "Email, password, and role are required." });
    }

    // Hash password
    const hashedPassword = await bcrypt.hash(password, 10);

    // 🎓 Student registration
    if (role === "student") {
      if (!username) {
        return res.status(400).json({ error: "Student name is required." });
      }

      const student = await Student.create({
        name: username,
        email,
        password: hashedPassword,
      });

      console.log("✅ Student created:", student.id);
      return res.status(201).json({
        message: "Student registered successfully",
        student: { id: student.id, email: student.email, name: student.name },
      });
    }

    // 🏢 Employer registration
    if (role === "employer") {
      if (!company_name) {
        return res.status(400).json({ error: "Company name is required for employers." });
      }

      const employer = await Employer.create({
        company_name,
        company_type,
        industry,
        company_description,
        email,
        password: hashedPassword,
      });

      console.log("✅ Employer created:", employer.id);
      return res.status(201).json({
        message: "Employer registered successfully",
        employer: { id: employer.id, email: employer.email, company_name: employer.company_name },
      });
    }

    // 🚫 Invalid role
    return res.status(400).json({ error: "Invalid role selected. Must be 'student' or 'employer'." });
  } catch (err) {
    console.error("❌ Registration error:", err);
    res.status(500).json({ error: "Internal server error" });
  }
});

// ✅ Login Route
router.post("/login", async (req, res) => {
  try {
    const { email, password, role } = req.body;

    if (!email || !password || !role) {
      return res.status(400).json({ error: "Email, password, and role are required." });
    }

    let user;
    if (role === "student") {
      user = await Student.findOne({ where: { email } });
    } else if (role === "employer") {
      user = await Employer.findOne({ where: { email } });
    } else {
      return res.status(400).json({ error: "Invalid role provided." });
    }

    if (!user) {
      return res.status(404).json({ error: "User not found." });
    }

    const isValid = await bcrypt.compare(password, user.password);
    if (!isValid) {
      return res.status(401).json({ error: "Invalid password." });
    }

    const token = jwt.sign(
      { id: user.id, role },
      process.env.JWT_SECRET || "default_secret",
      { expiresIn: "1d" }
    );

    res.status(200).json({
      message: "Login successful",
      token,
      user: { id: user.id, email: user.email, role },
    });
  } catch (err) {
    console.error("❌ Login error:", err);
    res.status(500).json({ error: "Internal server error" });
  }
});


export default router;
