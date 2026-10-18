/**
 * dashboardRoutes.js — Routing Endpoint Statistik Dashboard
 * Pengembang: Rana Shofa (Backend & Database Developer)
 */

const express = require('express');
const router = express.Router();
const dashboardController = require('../controllers/dashboardController');

// Endpoint publik / pemantauan dashboard
router.get('/stats', dashboardController.getDashboardStats);

module.exports = router;
