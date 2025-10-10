import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const Employer = sequelize.define("Employer", {
  company_name: { type: DataTypes.STRING, allowNull: false },
  email: { type: DataTypes.STRING, allowNull: false, unique: true },
  password: { type: DataTypes.STRING, allowNull: false },
  description: { type: DataTypes.TEXT },
  industry: { type: DataTypes.STRING },
  logo: { type: DataTypes.STRING },
  website_url: { type: DataTypes.STRING },
  contact_person: { type: DataTypes.STRING },
  location: { type: DataTypes.STRING },
});

export default Employer;
