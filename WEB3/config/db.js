import { Sequelize } from "sequelize";
import dotenv from "dotenv";

dotenv.config();

const sequelize = new Sequelize(
  process.env.DB_NAME,
  process.env.DB_USER,
  process.env.DB_PASSWORD,
  {
    host: process.env.DB_HOST,
    dialect: "mysql", // ✅ switched from postgres to mysql
    port: process.env.DB_PORT || 3306, // ✅ MySQL default port
    logging: false,
  }
);

export { sequelize };
