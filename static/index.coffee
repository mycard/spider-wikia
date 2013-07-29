info = list = null
state = 0 # 0-初始化 1-列表采集完成 2-内容采集完成 3-操作中
stateName = ['初始化', '列表采集完成', '内容采集完成', '操作中']
listInfo =
	page: 0
	url: ''

spiderList = ()->
	return alert stateName[state] if state > 0
	message '开始采集列表页...'
	state = 3
	spiderOneList = (page, url)->
		url = '' unless page and url
		state = 0
		throw 'url miss' unless typeof url is 'string'
		listInfo =
			page: page
			url: url
		message "正在采集第#{page}页"
		ajax baseUrl + 'app.php?action=spiderlist&url=' + encodeURIComponent(url), undefined, (res)->
			unless res.state
				return message "失败#{res.error}"
			message "成功"
			for item in res.data
				showList item.name, item.url

			if res.next
				# 继续处理下一页
				spiderOneList ++page, res.next
			else
				message '列表页采集完毕'
				state = 1
		, (res)->
			alert "失败(#{res.url})"

	showList = (name, url)->
		tr = document.createElement 'tr'
		tr.innerHTML = "<td>#{name}</td><td><a href=\"#{url}\" target=\"_blank\">link</a></td><td></td>"
		list.appendChild tr

	spiderOneList listInfo.page, listInfo.url


spiderContent = ()->
	index = length = 0
	return alert stateName[state] if state > 1
	message '开始采集内容页...'
	state = 3
	spiderOneContent = (tr)->
		name = tr.firstChild.innerHTML
		ajax baseUrl + 'app.php?action=spidercontent&name=' + name, undefined, (res)->
			if res.state
				tr.querySelectorAll('td')[2].innerHTML = '成功'
			else
				tr.querySelectorAll('td')[2].innerHTML = '失败'
			if index < length
				spiderOneContent trs.item index++
			else
				message '采集结束'
				state = 2
		, (res)->
			tr.querySelectorAll('td')[2].innerHTML = '失败'
			if index < length
				spiderOneContent trs.item index++
			else
				message '采集结束'
				state = 2

	trs = document.getElementById('list').querySelectorAll('tr')
	return alert '列表为空' unless trs.length
	length = trs.length
	index = 0
	spiderOneContent trs.item index++



message = (text)->
	info.value += "\r\n" if info.value.length > 0
	info.value += text

ajax = (url, data, success, error)->
	console.log url
	method = if typeof data is 'string' and data.length > 0 then 'post' else 'get'
	xhr = new XMLHttpRequest()
	xhr.open method, url, on
	xhr.onreadystatechange = ()->
		if xhr.readyState is 4
			if xhr.status is 200
				success JSON.parse xhr.responseText
			else
				error JSON.parse xhr.responseText
	xhr.send data


window.onload = ()->
	info = document.querySelector('#info')
	list = document.querySelector('#list')
	message '前端脚本加载完毕'
	document.querySelector('#spiderList').addEventListener 'click', ()->
		spiderList()
	document.querySelector('#spiderContent').addEventListener 'click', ()->
		spiderContent()
