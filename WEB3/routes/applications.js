import express from "express";
import { verifyToken, isStudent, isEmployer } from "../middleware/auth.js";
import Application from "../models/Application.js";

const router = express.Router();

// Student applies to job
router.post(
  "/:jobId",
  verifyToken,
  isStudent,
  async (req, res) => {
    try {
      const application = await Application.create({
        jobId: req.params.jobId,
        studentId: req.user.id,
        coverLetter: req.body.coverLetter,
      });
      res.json({ message: "✅ Application submitted", application });
    } catch (err) {
      res.status(500).json({ error: err.message });
    }
  }
);

// View my applications (student)
router.get("/my", verifyToken, isStudent, async (req, res) => {
  try {
    const applications = await Application.findAll({
      where: { studentId: req.user.id },
    });
    res.json(applications);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

export default router;
