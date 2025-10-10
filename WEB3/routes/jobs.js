import express from "express";
import Job from "../models/Job.js";
import { verifyToken, isEmployer } from "../middleware/auth.js";

const router = express.Router();

// Employer posts a job
router.post("/", verifyToken, isEmployer, async (req, res) => {
  try {
    const { title, description, location, type } = req.body;
    const job = await Job.create({
      title,
      description,
      location,
      type,
      employerId: req.user.id,
    });
    res.json({ message: "✅ Job created", job });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Public: view all jobs
router.get("/", async (req, res) => {
  try {
    const jobs = await Job.findAll();
    res.json(jobs);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Get a single job by ID
router.get("/:id", async (req, res) => {
  try {
    const job = await Job.findByPk(req.params.id);
    if (!job) return res.status(404).json({ error: "Job not found" });
    res.json(job);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

export default router;
