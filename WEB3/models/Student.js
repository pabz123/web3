import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const Student = sequelize.define("Student", {
  name: { type: DataTypes.STRING, allowNull: false },
  email: { type: DataTypes.STRING, allowNull: false, unique: true },
  password: { type: DataTypes.STRING, allowNull: false },
  contact: { type: DataTypes.STRING },
  location: { type: DataTypes.STRING },
  university: { type: DataTypes.STRING },
  course: { type: DataTypes.STRING },
  graduation_year: { type: DataTypes.INTEGER },
  gpa: { type: DataTypes.DECIMAL(3, 2) },
  skills: { type: DataTypes.TEXT },
  experience: { type: DataTypes.TEXT },
  portfolio_links: { type: DataTypes.TEXT },
  job_preferences: { type: DataTypes.TEXT },
  profile_visibility: { type: DataTypes.BOOLEAN, defaultValue: true },
  cv_file: { type: DataTypes.STRING },
});

export default Student;
