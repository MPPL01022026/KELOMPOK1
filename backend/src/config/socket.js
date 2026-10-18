/**
 * socket.js — Konfigurasi Real-Time Socket.IO
 * Mengatur event pembaruan stok untuk Rak Digital & Dashboard
 */

const { Server } = require('socket.io');

let io = null;

function initSocket(server) {
    io = new Server(server, {
        cors: {
            origin: '*', // Mengizinkan koneksi dari frontend mana pun
            methods: ['GET', 'POST']
        }
    });

    io.on('connection', (socket) => {
        console.log(`🔌 Klien terhubung ke WebSocket: ${socket.id}`);

        socket.on('disconnect', () => {
            console.log(`❌ Klien terputus: ${socket.id}`);
        });
    });

    return io;
}

function getIO() {
    if (!io) {
        throw new Error('Socket.IO belum diinisialisasi!');
    }
    return io;
}

// Fungsi pembantu untuk memancarkan event pembaruan stok ke semua klien yang terhubung
function emitStockUpdate(data) {
    if (io) {
        io.emit('stock_updated', {
            timestamp: new Date().toISOString(),
            ...data
        });
        console.log('📡 Event stock_updated dipancarkan:', data);
    }
}

module.exports = {
    initSocket,
    getIO,
    emitStockUpdate
};
