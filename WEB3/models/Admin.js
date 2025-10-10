import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const Admin = sequelize.define("Admin", {
  username: { type: DataTypes.STRING, unique: true },
  password: { type: DataTypes.STRING, allowNull: false },
  email: { type: DataTypes.STRING },
  role: {
    type: DataTypes.ENUM("superadmin", "moderator"),
    defaultValue: "moderator",
  },
});

export default Admin;
