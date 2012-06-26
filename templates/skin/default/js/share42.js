/*
 * http://share42.com
 * Date: 30.08.2011
 * (c) 2011, Dimox
 */
function share42(jid, f, u, t) {
	if (!u)
		u = location.href;
	if (!t)
		t = document.title;
	u = encodeURIComponent(u);
	t = encodeURIComponent(t);
	jo = $('#' + jid);
	jo.empty();
	
	html = '<div id="share42">';
	var s = new Array(
			'"http://www.facebook.com/sharer.php?u=' + u + '&t=' + t
					+ '" title="Поделиться в Facebook"',
			'"http://www.livejournal.com/update.bml?event=' + u + '&subject='
					+ t + '" title="Опубликовать в LiveJournal"',
			'"http://connect.mail.ru/share?url=' + u + '&title=' + t
					+ '" title="Поделиться в Моем Мире@Mail.Ru"',
			'"http://www.odnoklassniki.ru/dk?st.cmd=addShare&st._surl=' + u
					+ '&title=' + t + '" title="Добавить в Одноклассники"',
			'"http://twitter.com/share?text=' + t + '&url=' + u
					+ '" title="Добавить в Twitter"',
			'"#" onclick="window.open(\'http://vkontakte.ru/share.php?url='
					+ u
					+ '\', \'_blank\', \'scrollbars=0, resizable=1, menubar=0, left=200, top=200, width=554, height=421, toolbar=0, status=0\');return false" title="Поделиться В Контакте"',
			'"http://www.feedburner.com/fb/a/emailFlare?loc=ru_RU&itemTitle='
					+ t + '&uri=' + u + '" title="Отправить на e-mail другу"');
	for (i = 0; i < s.length; i++)
		html += ('<a rel="nofollow" style="display:inline-block;width:16px;height:16px;margin:0 7px 7px 0;background:url('
						+ f
						+ 'icons.png) -'
						+ 16
						* i
						+ 'px 0" href='
						+ s[i]
						+ ' target="_blank"></a>');
						
	html += '</div>';
	jo.html(html);
}
