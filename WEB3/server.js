import express from "express";
import dotenv from "dotenv";
import { sequelize } from "./config/db.js";
import authRoutes from "./routes/auth.js";
import jobRoutes from "./routes/jobs.js";
import studentRoutes from "./routes/student.js";
import applicationRoutes from "./routes/applications.js";
import cors from "cors";
import "./models/Student.js";
import "./models/Employer.js";
import "./models/Job.js";
import "./models/Application.js";
import "./models/Admin.js";
import path from "path";
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

dotenv.config();

const app = express();

// Updated CORS configuration
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));


// Serve static files
app.use(express.static(path.join(__dirname, 'pages')));
app.use('/css', express.static(path.join(__dirname, 'css')));
app.use('/js', express.static(path.join(__dirname, 'js')));
app.use('/assets', express.static(path.join(__dirname, 'assets')));
app.use(express.static("/uploads"));
// Serve home explicitly at root so visiting / shows the home page
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'pages', 'home.html'));
});

// Routes
app.use("/auth", authRoutes);
app.use("/jobs", jobRoutes);
app.use("/applications", applicationRoutes);
app.use("/api/student", studentRoutes);


// Error handling middleware
app.use((err, req, res, next) => {
    console.error(err.stack);
    res.status(500).json({ error: err.message });
});

app.post('/auth/logout', (req, res) => {
  req.session?.destroy?.(() => {
    res.clearCookie('connect.sid'); // if using express-session
    res.status(200).json({ message: 'Logged out' });
  }) || res.json({ message: 'No session active' });
});


sequelize
    .sync({ alter: true })
    .then(() => console.log("✅ MySQL database synced"))
    .catch((err) => console.error("❌ DB sync error:", err));

const PORT = process.env.PORT || 5000;
app.listen(PORT, () => console.log(`🚀 Server running at http://localhost:${PORT}`));