import urllib
for i in range(0, 613):
	urllib.urlretrieve("http://identicon.relucks.org/" + str(i) + "?size=200", "../public/avatar/" + str(i)+".png")
