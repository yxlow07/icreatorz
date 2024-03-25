import express from 'express';
import { createServer } from 'node:http';
import { Server } from 'socket.io';

const port = 3308
const host = 'http://192.168.148.1'

const app = express();
const server = createServer(app);
const io = new Server(server, {
  cors: {
    origin: host,
    methods: ['GET', 'PUT'],
    allowedHeaders: ['localhost']
  }
});

io.on('connection', (socket) => {
    socket.on('announcement', (msg) => {
      console.log(msg)
      io.emit('announcement', msg);
    })

    socket.on('reply', (msg) => {
        console.log(msg)
        io.emit('reply', msg);
    })
});

server.listen(port, () => {
  console.log('server running at ');
});