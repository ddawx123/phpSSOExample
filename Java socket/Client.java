package tcp;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintStream;
import java.net.Socket;
import java.net.SocketTimeoutException;

/**
 * Created by jy on 2017/9/21.
 */
public class Client {

    public static void main(String[] strings) {
        try {
            Socket client = new Socket("127.0.0.1", 20000);

            client.setSoTimeout(10000);

            // 获取键盘输入
            BufferedReader input = new BufferedReader(new InputStreamReader(System.in));

            // 获取 socket 输出流，用来向服务端发送数据
            PrintStream out = new PrintStream(client.getOutputStream());

            //获取 socket 输入流，从服务端获取数据
            BufferedReader buf = new BufferedReader(new InputStreamReader(client.getInputStream()));

            boolean flag = true;
            while (flag) {
                System.out.print("输入信息: ");
                String str = input.readLine();
                //发送数据到服务端
                out.println(str);
                if ("".equals(str)) {
                    continue;
                }
                if ("bye".equals(str)) {
                    flag = false;
                } else {
                    try {
                        //从服务器端接收数据有个时间限制（系统自设，也可以自己设置），超过了这个时间，便会抛出该异常
                        String echo = buf.readLine();
                        System.out.println(echo);
                    } catch (SocketTimeoutException e) {
                        System.out.println("Time out, No response");
                    }
                }
            }
            input.close();
            if (client != null) {
                client.close();
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
