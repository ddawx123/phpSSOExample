package tcp;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintStream;
import java.net.ServerSocket;
import java.net.Socket;

/**
 * Created by jy on 2017/9/21.
 */
public class Server {

    public static void main(String[] strings) {
        try {
            ServerSocket serverSocket = new ServerSocket(20000);

            Socket client = null;

            boolean f = true;
            while (f) {
                // accept 阻塞，直到有客户端连接，返回一个 socket 实例
                // 通过这个 socket 获取到 inputStream 和 outputStream 用来写数据和读数据
                client = serverSocket.accept();
                System.out.println("客户端连接成功");
                new Thread(new ServerThread(client)).start();
            }
            serverSocket.close();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

}


class ServerThread implements Runnable {

    private Socket client = null;

    public ServerThread(Socket client) {
        this.client = client;
    }

    @Override
    public void run() {
        try {
            // 用来向客户端发送数据
            PrintStream out = new PrintStream(client.getOutputStream());

            // 获取客户端数据
            // 带有缓冲区的输入流，
            BufferedReader buf = new BufferedReader(new InputStreamReader(client.getInputStream()));

            boolean flag = true;
            while (flag) {
                // readline 会阻塞，除非对方明确调用了 close
                // readline 在达到 buffer 大小之前，只有遇到\r \n 就会返回
                String str = buf.readLine();
                if (str == null || "".equals(str)) {
                    flag = false;
                } else {
                    if ("bye".equals(str)) {
                        flag = false;
                    } else {
                        out.println("get");
                    }
                }
            }
            out.close();
            client.close();

        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}