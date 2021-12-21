#!/bin/bash

#TODO 随机可用端口
ports=(9603 9604)

# 检查 http服务端口
check_port() {
    # shellcheck disable=SC1083
    code=$(curl -I -m 10 -o /dev/null -s -w %{http_code}  127.0.0.1:"$1"/check_port)
    echo "$code"
}

# 检查 socket服务端口
socket_io_check_port() {
    port=$1
    # shellcheck disable=SC2196
    TCP=$(netstat -an | egrep ":${port}" | awk '$1 == "tcp" && $NF == "LISTEN" ' |wc -l)
    # shellcheck disable=SC2196
    UDP=$(netstat -an | egrep ":${port}" | awk '$1 == "udp" && $NF == "0.0.0.0:*"' |wc -l)

    ((Total = TCP + UDP ))

    if [ "${Total}" == 0 ];
      then
        echo 400
      else
        echo 200
    fi
}

#TODO 随机API可用端口
online_port=0
reload_port=0

for i in "${!ports[@]}";
do
    code=$(check_port "${ports[$i]}")
    # shellcheck disable=SC2053
    if [[ '200' == $code ]]; then
        online_port=${ports[$i]}
    else
        reload_port=${ports[$i]}
    fi
done

if [[ $online_port == 0 ]]; then
    echo '服务未启动'
    reload_port=${ports[0]}
    echo "自动分配端口 $reload_port"
else
    echo "当前端口 $online_port"
    echo "重启端口 $reload_port"
fi

if [[ $reload_port == 0 ]]; then
	echo '部署失败, 未获取到可用端口'
	exit 1;
fi



#TODO 随机socket.io可用端口
socket_io_ports=(9606 9607)
socket_io_online_port=0
socket_io_reload_port=0
for i in "${!socket_io_ports[@]}";
do
    code=$(socket_io_check_port "${socket_io_ports[$i]}")
    # shellcheck disable=SC2053
    if [[ '200' == $code ]]; then
        socket_io_online_port=${socket_io_ports[$i]}
    else
        socket_io_reload_port=${socket_io_ports[$i]}
    fi
done
if [[ $socket_io_online_port == 0 ]]; then
    echo '服务未启动'
    socket_io_reload_port=${socket_io_ports[0]}
    echo "socket.io自动分配端口 $socket_io_reload_port"
else
    echo "socket.io当前端口 $socket_io_online_port"
    echo "socket.io重启端口 $socket_io_reload_port"
fi

if [[ $socket_io_reload_port == 0 ]]; then
	echo '部署失败, 未获取到可用socket.io端口'
	exit 1;
fi

# nginx配置文件路径
nginx_upstream="/www/server/panel/vhost/upstream/upstream_hyperf.conf"

# shellcheck disable=SC2016
upstream='
upstream hyperf {
    server 127.0.0.1:'"$reload_port"';
}
upstream hyperf_socket_io {
    server 127.0.0.1:'"$socket_io_reload_port"';
}
'

git pull
docker build -t hyperf_server:hyperf_cli .
echo "docker run -p $reload_port:9602 -p $socket_io_reload_port:9606 --name hyperf_server_$reload_port_$socket_io_reload_port   -dit hyperf_server:hyperf_cli"
docker run -p "$reload_port":9602 -p $socket_io_reload_port:9606 --name hyperf_server_"$reload_port"_"$socket_io_reload_port"   -dit hyperf_server:hyperf_cli

while :
do
    reload=$(check_port "$reload_port")
    socket_io_reload=$(socket_io_check_port "$socket_io_reload_port")
    echo "check hyperf_server_$reload_port 服务 reload $reload"
    echo "check socket.io—port $socket_io_reload_port 服务 reload $socket_io_reload"
    # shellcheck disable=SC2053
    if [[ '200' == $reload && '200' == $socket_io_reload ]]; then
        echo "新服务重启成功 hyperf_server_$reload_port""_""$socket_io_reload_port"
        echo "$upstream" > $nginx_upstream
        nginx -s reload
        if [[ $online_port != 0 ]]; then
            # 无法探知老服务持有链接响应完成时间
            sleep 5
            echo "停止旧服务 hyperf_server_$online_port""_""$socket_io_reload_port"
            # shellcheck disable=SC2027
            docker stop "hyperf_server_""$online_port""_""$socket_io_reload_port"
            docker rm -f "hyperf_server_""$online_port""_""$socket_io_reload_port"
            break
        else
            break
        fi
    fi
    sleep 2
done