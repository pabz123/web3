import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";
import Student from "./Student.js";
import Job from "./Job.js";

const Application = sequelize.define("Application", {
  coverLetter: { type: DataTypes.TEXT },
  status: {
    type: DataTypes.ENUM("Applied", "Reviewed", "Interview", "Hired", "Rejected"),
    defaultValue: "Applied",
  },
});

// Relations
Student.hasMany(Application, { foreignKey: "studentId", onDelete: "CASCADE" });
Application.belongsTo(Student, { foreignKey: "studentId" });

Job.hasMany(Application, { foreignKey: "jobId", onDelete: "CASCADE" });
Application.belongsTo(Job, { foreignKey: "jobId" });

export default Application;
