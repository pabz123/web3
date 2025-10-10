import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";
import Employer from "./Employer.js";

const Job = sequelize.define("Job", {
  title: { type: DataTypes.STRING, allowNull: false },
  type: { type: DataTypes.ENUM("Internship", "Full-time", "Part-time", "Contract"), allowNull: false },
  industry: { type: DataTypes.STRING },
  description: { type: DataTypes.TEXT },
  responsibilities: { type: DataTypes.TEXT },
  requirements: { type: DataTypes.TEXT },
  location: { type: DataTypes.STRING },
  application_deadline: { type: DataTypes.DATE },
  application_method: { type: DataTypes.ENUM("Direct", "External"), defaultValue: "Direct" },
  external_link: { type: DataTypes.STRING },
  status: { type: DataTypes.ENUM("Open", "Closed"), defaultValue: "Open" },
});

// Relations
Employer.hasMany(Job, { foreignKey: "employer_id", onDelete: "CASCADE" });
Job.belongsTo(Employer, { foreignKey: "employer_id" });

export default Job;
