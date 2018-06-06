## 反馈
- 如何调错
    1. 确定是否是用域名或ip打开网页的
    2. 确定是否没有中文
    3. 查看console看一下有没有报错
    4. 查看network数据是否返回正确

## 推导过程
- 说明：整个过程是为了刻意去模仿jquery中的$.ajax方法，$.get,$.post方法，所以大家如果想不到是正常的
- 先看一下我们的接口文档（涉及到get_json.php、post_json.php、data.xml）

- 1.html演示如何使用data.txt这个数据接口（为了复习，可以重新写一遍）
- 2.html演示如何使用get_json.php这个数据接口
- 3.html演示如何使用post_json.php这个数据接口
- 4.html演示如何使用data.xml这个数据接口

- 先看2.html思考：我们怎么封装成一个函数?

```js
var xhr = new XMLHttpRequest();
xhr.open('get','./get_json.php?username='+username+'&age='+age);
xhr.send(null);
xhr.onreadystatechange = function(){
    if(xhr.readyState == 4 && xhr.status == 200){
        var obj = JSON.parse(xhr.responseText);
        console.log(obj);
    }
}
```


    + 放到一个函数当中

```js
function ajax(){
    var xhr = new XMLHttpRequest();
    xhr.open('get','./get_json.php?username='+username+'&age='+age);
    xhr.send(null);
    xhr.onreadystatechange = function(){
        if(xhr.readyState == 4 && xhr.status == 200){
            var obj = JSON.parse(xhr.responseText);
            console.log(obj);
        }
    }
}
```

    + 封装的思路（先假设数据请求回来的都是json类型的）(4.html)
        1. 提取代码中的可变的东西，作为形参
            - 'get' --> type
            - './get_json.php' --> url
            - 'username='+username+'&age='+age --> data
            - console.log(obj); --> success

```html
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script>
        function ajax(type,url,data,success){
            var xhr = new XMLHttpRequest();
            xhr.open(type, url+'?'+data);
            xhr.send(null);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var obj = JSON.parse(xhr.responseText);
                    success(obj);
                }
            }
        }
        window.onload = function () {
            var btn = document.getElementById('btn');
            btn.onclick = function () {
                var username = document.getElementById('username').value;
                var age = document.getElementById('age').value;
                ajax('get','./get_json.php','username='+username+'&age='+age,function(data){
                    console.log(data);
                })
            }
        };
    </script>
</head>

<body>
    姓名：
    <input type="text" name="username" id="username"> 年龄：
    <input type="number" name="age" id="age">
    <input type="button" value="点击发送ajax" id="btn">
    <p id="cont"></p>
</body>

</html>
```


    + 数据如果让用户手动的拼接也可以，但是我们还可以让用户更懒一点，添加一个getpa方法，用来处理data(6.html)

```html
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script>
        function ajax(type,url,data,success){
            var xhr = new XMLHttpRequest();
            xhr.open(type, url+'?'+getPa(data));
            xhr.send(null);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var obj = JSON.parse(xhr.responseText);
                    success(obj);
                }
            }
        }
        
    
        /**
         * getPa 把对象类型的data转换成a=1&b=2的形式
         * @return mixed 
         */
        function getPa(data){
            var str = "";
            for (var i in data) {
                str = str + i + "=" + data[i] + "&";
            }
           return str.slice(0, str.length - 1);
        }

        window.onload = function () {
            var btn = document.getElementById('btn');
            btn.onclick = function () {
                var username = document.getElementById('username').value;
                var age = document.getElementById('age').value;
                ajax('get','./get_json.php',{username:username,age:age},function(data){
                    console.log(data);
                })
            }
        };
    

        
    </script>
</head>

<body>
    姓名：
    <input type="text" name="username" id="username"> 年龄：
    <input type="number" name="age" id="age">
    <input type="button" value="点击发送ajax" id="btn">
    <p id="cont"></p>
</body>

</html>
```


    + 为了防止函数名被全局函数污染,同时为了把ajax,getpa方法放在一起便于维护和管理，使用$.ajax的形式(7.html)

```html
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script>
        var $ = {
            ajax:function (type, url, data, success) {
                var xhr = new XMLHttpRequest();
                xhr.open(type, url + '?' + this.getPa(data));
                xhr.send(null);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        var obj = JSON.parse(xhr.responseText);
                        success(obj);
                    }
                }
            },
            getPa:function (data) {
                var str = "";
                for (var i in data) {
                    str = str + i + "=" + data[i] + "&";
                }
                return str.slice(0, str.length - 1);
            }
        };

        window.onload = function () {
            var btn = document.getElementById('btn');
            btn.onclick = function () {
                var username = document.getElementById('username').value;
                var age = document.getElementById('age').value;
                $.ajax('get','./get_json.php',{username:username,age:age},function(data){
                    console.log(data);
                })
            }
        };
    

        
    </script>
</head>

<body>
    姓名：
    <input type="text" name="username" id="username"> 年龄：
    <input type="number" name="age" id="age">
    <input type="button" value="点击发送ajax" id="btn">
    <p id="cont"></p>
</body>

</html>
```


- 再看3.html
    + 如果请求的方法是post,则添加xhr.setRequestHeader
    + 如果请求的方法是post,则参数键值对放在xhr.send(8.html)

```html
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script>
        var $ = {
            ajax:function (type, url, data, success) {
                var xhr = new XMLHttpRequest();
                if(type == 'get'){
                    xhr.open(type, url + '?' + this.getPa(data));
                    xhr.send(null);
                }else{
                    xhr.open(type, url);
                    xhr.setRequestHeader('content-type','application/x-www-form-urlencoded');
                    xhr.send(this.getPa(data));
                }
                
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        var obj = JSON.parse(xhr.responseText);
                        success(obj);
                    }
                }
            },
            getPa:function (data) {
                var str = "";
                for (var i in data) {
                    str = str + i + "=" + data[i] + "&";
                }
                return str.slice(0, str.length - 1);
            }
        };

        window.onload = function () {
            var btn = document.getElementById('btn');
            btn.onclick = function () {
                var username = document.getElementById('username').value;
                var age = document.getElementById('age').value;
                $.ajax('post','./post_json.php',{username:username,age:age},function(data){
                    console.log(data);
                })
            }
        };
    

        
    </script>
</head>

<body>
    姓名：
    <input type="text" name="username" id="username"> 年龄：
    <input type="number" name="age" id="age">
    <input type="button" value="点击发送ajax" id="btn">
    <p id="cont"></p>
</body>

</html>
```


- 再看1.html,4.html --> 我们前面只考虑了json一种返回数据类型，这一次，要加上text,xml两种类型数据
    + 参数再加一个dataType

```html
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script>
        var $ = {
            ajax:function (type,url, data, success,dataType) {
                var xhr = new XMLHttpRequest();
                if(type == 'get'){
                    xhr.open(type, url + '?' + this.getPa(data));
                    xhr.send(null);
                }else{
                    xhr.open(type, url);
                    xhr.setRequestHeader('content-type','application/x-www-form-urlencoded');
                    xhr.send(this.getPa(data));
                }
                
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        if(dataType == 'text'){
                            var obj = xhr.responseText;
                            success(obj);
                        }else if(dataType == 'xml'){
                            var result = xhr.responseXML;
                            success(result);
                        //默认是json类型
                        } else{
                            var obj = JSON.parse(xhr.responseText);
                            success(obj);
                        }
                    }
                }
            },
            getPa:function (data) {
                var str = "";
                for (var i in data) {
                    str = str + i + "=" + data[i] + "&";
                }
                return str.slice(0, str.length - 1);
            }
        };

        $.ajax('get', './data.xml', {}, function (data) {
                console.log(data);
            },'xml');

        $.ajax('get', './data.txt', {}, function (data) {
                console.log(data);
            },'text');

        $.ajax('get','./get_json.php', {username:'jack',age:20}, function (data) {
                console.log(data);
            },'json');
        $.ajax('post', './post_json.php', {username:'jack',age:20}, function (data) {
                console.log(data);
            },'json')

        
    </script>
</head>

<body>
    
</body>

</html>
```

- 再来思考，参数太多了，就存在顺序一说，不太好记每个参数的顺序
    + 使用对象来做为参数(10.html)

```html
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script>
        var $ = {
            ajax:function (option) {
                var xhr = new XMLHttpRequest();
                if(option.type == 'get'){
                    xhr.open(option.type, option.url + '?' + this.getPa(option.data));
                    xhr.send(null);
                }else{
                    xhr.open(option.type, option.url);
                    xhr.setRequestHeader('content-type','application/x-www-form-urlencoded');
                    xhr.send(this.getPa(option.data));
                }
                
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        if(option.dataType == 'text'){
                            var obj = xhr.responseText;
                            option.success(obj);
                        }else if(option.dataType == 'xml'){
                            var result = xhr.responseXML;
                            option.success(result);
                        //默认是json类型
                        } else{
                            var obj = JSON.parse(xhr.responseText);
                            option.success(obj);
                        }
                    }
                }
            },
            getPa:function (data) {
                var str = "";
                for (var i in data) {
                    str = str + i + "=" + data[i] + "&";
                }
                return str.slice(0, str.length - 1);
            }
        };

        $.ajax({
            type:'get',
            url: './data.xml',
            data:{},
            success: function (data) {
                console.log(data);
            },
            dataType:'xml'
        });
    </script>
</head>

<body>
    
</body>

</html>
```

- 当然，我们封装的这个函数如果为了保证健壮性，还要考虑的东西有很多(这块不是重点要考虑的，我们了解即可 11.html)
    + 万一用户传入的success方法不是一个函数
    + 万一用户没有传入dataType --> 默认值是json
    + 万一用户没有传入type --> 默认是get

```html
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script>
        var $ = {
            ajax:function (option) {
                //默认值
                option.type = option.type || 'get';
                option.dataType = option.dataType || 'json';

                var xhr = new XMLHttpRequest();
                if(option.type == 'get'){
                    xhr.open(option.type, option.url + '?' + this.getPa(option.data));
                    xhr.send(null);
                }else{
                    xhr.open(option.type, option.url);
                    xhr.setRequestHeader('content-type','application/x-www-form-urlencoded');
                    xhr.send(this.getPa(option.data));
                }
                
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        if(option.dataType == 'text'){
                            var obj = xhr.responseText;
                            option.success(obj);
                        }else if(option.dataType == 'xml'){
                            var result = xhr.responseXML;
                            option.success(result);
                        //默认是json类型
                        } else{
                            var obj = JSON.parse(xhr.responseText);
                            typeof option.success == 'function' && option.success(obj);
                        }
                    }
                }
            },
            getPa:function (data) {
                var str = "";
                for (var i in data) {
                    str = str + i + "=" + data[i] + "&";
                }
                return str.slice(0, str.length - 1);
            }
        };

        $.ajax({
            type:'get',
            url: './data.xml',
            data:{},
            success: function (data) {
                console.log(data);
            },
            dataType:'xml'
        });
    </script>
</head>

<body>
    
</body>

</html>
```


- 思考：我们一般的情况下用get和post请求json数据是最常见的情况
    + 封装一个$.get(12.html)
    + 封装一个$.post

