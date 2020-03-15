React常见问题解决
===========

本文是基于Windows 10系统环境，学习和使用React：

*   **Windows 10**

* * *

一、react常见问题解决方案
---------------

### (1) 拼接字符串（常量+变量）

    const variable = 'department'
    const param1 = 'inspur'
    const param2 = 'chaoyue'
    <a href={'https://wap.inspur.com/' + variable}>
    	${param1}--${param2}
    </a>
    //inspur--chaoyue
    

    <FormItem label={formatMessage({ id: 'app.mail.read' })}>
        {getFieldDecorator('senderInfo', {
            initialValue: `${sender}--${time}`
        }
        )(
            <Input readOnly />
        )}
    </FormItem>
    

### (2) 阻止默认事件和冒泡

    handleCheck = (e, item) => {
            e.stopPropagation();
            console.log('handleCheck');
            console.log(item);
        }
    

### (3) 设置启动的端口号

    ## 打开react项目的 package.json文件
    ## 将 scripts中的start键值对
      "start": "react-app-rewired start"
    ## 修改为
      "start": "set PORT=3008&&react-app-rewired start",
    

二、ant design常见问题解决方案
--------------------

### (1) 设置Select下拉框的显示层级

    <Select dropdownStyle={{ zIndex: 10001 }}>
        <Option value='chaoyue'>浪潮超越</Option>
    </Select>
    

* * *

*   [点赞](javascript:;)
*   [收藏](javascript:;)
*   [分享](javascript:;)
*   *   文章举报

 [![](https://profile.csdnimg.cn/D/1/2/3_qq_32599479) ![](https://g.csdnimg.cn/static/user-reg-year/2x/4.png)](https://blog.csdn.net/qq_32599479) 