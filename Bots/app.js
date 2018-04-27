var Discord = require('discord.io');
var request = require("request");
var url = require('url');
var Twit = require('twit');
var fs = require('fs');

var serverreply;
var urlparse;
var serversname = {};
var loginscount = 0;

var weekday = new Array(7);
weekday[0] =  "Sunday";
weekday[1] = "Monday";
weekday[2] = "Tuesday";
weekday[3] = "Wednesday";
weekday[4] = "Thursday";
weekday[5] = "Friday";
weekday[6] = "Saturday";

var monthname = new Array(12);

function pauza(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

function sendStatus(channelid, servername, status, x) {
    if(status == "Online") {
        bot.sendMessage({
            to: channelid,
            embed: {
                color: 3066993,
                author: {
                    name: servername,
                    url: "https://launcher.soapboxrace.world/stats/"
                },
                description: '**' + servername + '** is back. Now you can join and play. :D',
                timestamp: x.toISOString()
            }
        });
    } else {
        bot.sendMessage({
            to: channelid,
            embed: {
                color: 15158332,
                author: {
                    name: servername,
                    url: "https://launcher.soapboxrace.world/stats/"
                },
                description: 'Uh oh. We just lost connection with **' + servername + '**. :(',
                timestamp: x.toISOString()
            }
        });
    }
}

function sendToDiscord(channelid, whichserver, jsonstatus, x) {
	if(jsonstatus.servername == whichserver) {
		sendStatus(channelid, jsonstatus.servername, jsonstatus.status, x);
	}	
}

var T = new Twit({
  consumer_key:         '',
  consumer_secret:      '',
  access_token:         '',
  access_token_secret:  '',
  timeout_ms:           60*1000,  // optional HTTP request timeout to apply to all requests.
})

console.log("Booting up");

var bot = new Discord.Client({
    token: "",
    autorun: true
});

bot.on("disconnect", function(event) {
	console.log("Bot disconnected");
	bot.connect();
    T.post('statuses/update', { status: 'I\'ve just crashed :(' }, function(err, data, response) {  })
});

bot.on('ready', function() {
    console.log('Logged in as %s - %s\n', bot.username, bot.id);
    bot.setPresence({game: { name:"MeTonaTOR", type: 1, url:"http://twitch.tv/metonator" }});

	fs.watchFile("serverstatus.json", function(curr,prev) {
		if(curr != prev) {
			var x = new Date();
			var timeformat = "[" + x.getUTCDate() + "/" + x.getUTCMonth() + "/" + x.getUTCFullYear() + " " + x.getUTCHours() + ":" + x.getUTCMinutes() + ":" + x.getUTCSeconds() + " UTC]";
			var jsonstatus = fs.readFileSync("serverstatus.json", 'utf8');
			jsonstatus = JSON.parse(jsonstatus);
			console.log(jsonstatus.servername + ": " + jsonstatus.status);
			
			if(jsonstatus.status == "Offline") {
				T.post('statuses/update', { status: timeformat + ' Looks like ' + jsonstatus.servername + ' is currenly offline.' }, function(err, data, response) { })
			} else {
				T.post('statuses/update', { status: timeformat + ' YAY! ' + jsonstatus.servername + ' is back online.' }, function(err, data, response) { })
			}

	        /* GLOBALLY (#info @ SBRW) */
	        sendStatus('425230837563260928', jsonstatus.servername, jsonstatus.status, x);
	        sendToDiscord('371703246802255873', "APEX Open Beta", jsonstatus, x);
	        sendToDiscord('417860255666536458', "SBRW Official Spanish Server (ES)", jsonstatus, x);
	    }
	});
});

bot.on('message', function(user, userID, channelID, message, event) {
    var splitted = message.split(" ");
    console.log(channelID + " - " + message);
    var ServerID = (typeof bot.channels[channelID] !== 'undefined') ? bot.channels[channelID].guild_id : 0;

    //Komendy
    var msg = message.replace('%cid%', channelID); //ChannelID
    msg = msg.replace('%uid%', userID); //UserID
    msg = msg.replace('%username%', user); //User
    msg = msg.replace('%time%', new Date()); //User
    msg = msg.replace('%events%', '```' + JSON.stringify(event) + '```'); // Events

    if(splitted['0'] == "$changenickname" || splitted['0'] == "$cn") {
      bot.deleteMessage({"channelID": channelID, "messageID": event['d']['id']});

      if(userID == "133384493493911552" || userID == "183624578105344001") {
        var realnick = msg.replace(splitted['1'] + '', '').replace(splitted['0'] + " ", "");
        var useridtorename = splitted['1'].replace("<@", "").replace(">", "").replace("!", "");

        bot.editNickname({"serverID": ServerID, "userID": useridtorename, "nick": realnick}, function(error, response) {
          var realnick = msg.replace(splitted['1'] + '', '').replace(splitted['0'] + " ", "");
          var useridtorename = splitted['1'].replace("<@", "").replace(">", "").replace("!", "");

          if(error) {
            bot.sendMessage({ to: channelID, embed: { color: 10038562, author: { name: "", }, description: "Failed to change <@" + useridtorename + "> nickname. " + response } });
          } else {
            bot.sendMessage({ to: channelID, embed: { color: 15844367, author: { name: "", }, description: "Changed <@" + useridtorename + "> nickname to `" + realnick + "`" } });
          }
        });
      } else {
        var useridtorename = splitted['1'].replace("<@", "").replace(">", "").replace("!", "");
        bot.sendMessage({ to: channelID, embed: { color: 10038562, author: { name: "", }, description: "<@" + userID + ">: You can't change <@" + useridtorename + ">'s nickname."} });
      }

      useridtorename = "";
      realnick = "";
    }

    if (userID == "133384493493911552" || userID == "273401279840256010" || userID == "183624578105344001" || userID == "249992273385750528" || userID == "312299042812198922") {
        if (splitted['0'] == "$say") {
            bot.sendMessage({
                to: channelID,
                embed: {
                    color: 15844367,
                    author: {
                        name: "",
                    },
                    fields: [{
                        name: 'Message from ' + user,
                        value: msg.replace('$say ', ''),
                        inline: true
                    }]
                }
            });
        }

        if (message == "bot, tell him wtf happens when offline server appears") {
            bot.sendMessage({
                to: channelID,
                embed: {
                    color: 15844367,
                    author: {
                        name: "",
                    },
                    fields: [{
                        name: "Bot says",
                        value: "Disable firewall ffs...",
                        inline: true
                    }]
                }
            });
        }
    }

    if (splitted['0'] == "$magicalword") {
        bot.sendMessage({
            to: channelID,
            embed: {
                color: 15844367,
                author: {
                    name: "",
                },
                fields: [{
                    name: 'Guess what?', //:black_large_square:
                    value: ":regional_indicator_a: :regional_indicator_m: :regional_indicator_a: :regional_indicator_t: :regional_indicator_e: :regional_indicator_u: :regional_indicator_r:",
                    inline: false
                }]
            }
        });
    }

    //http://api.icndb.com/jokes/random?firstName=John%20HE&lastName=REPLACEMENTS&limitTo=[nerdy]

    if (splitted['0'] == "$joke") {
    	request({ url: "http://api.icndb.com/jokes/random?firstName=" + user + "&lastName=REPLACEMENTS_VALUE&limitTo=[nerdy]" }, function(error, response, body) {
    		if (!error && response.statusCode === 200) {
				var obj = JSON.parse(body);

		        bot.sendMessage({
		            to: channelID,
		            embed: {
		                color: 15844367,
		                author: {
		                    name: "",
		                },
		                fields: [{
		                    name: 'Joke #' + obj.value.id, //:black_large_square:
		                    value: (obj.value.joke).replace(" REPLACEMENTS_VALUE", ""),
		                    inline: false
		                }]
		            }
		        });
    		} else {
    			//parse it

		        bot.sendMessage({
		            to: channelID,
		            embed: {
		                color: 15844367,
		                author: {
		                    name: "",
		                },
		                fields: [{
		                    name: 'Joke', //:black_large_square:
		                    value: "Failed to fetch joke.",
		                    inline: false
		                }]
		            }
		        });
    		}
    	});
    }

    if (splitted['0'] == "$racerestart") {
        var time = Math.floor(Date.now()/1000);
        var restart = 900;
        var next_seconds = restart - (time % restart);
        var next_min = Math.floor(next_seconds/60);
        var next_secs_remaining = next_seconds - (next_min*60);

        bot.sendMessage({
            to: channelID,
            embed: {
                color: 15158332,
                author: {
                    name: "",
                },
                description: "**Possible racing core restart:** " + (next_min + "m" + next_secs_remaining + "s"),
            }
        });
    }

    if (splitted['0'] == "$discord") {
        request({ url: "http://mirror.nfsw.mtntr.eu/stats/json.php" }, function(error, response, body) {
            serverreply = JSON.parse(body);
            var guess = "";
            var guess2 = "";
            for (var i = 0; i < serverreply.length; i++) {
                if(serverreply[i].social) {
                    if(serverreply[i].social.discord != 'nope' && serverreply[i].social.discord != "Your discord server url") {
                        guess += "**" + serverreply[i].serverName + ":** " + serverreply[i].social.discord + "\r\n";
                        guess2 = guess2 + ', {"name": "'+serverreply[i].serverName+'", "value": "- '+serverreply[i].social.discord+' ", "inline": true}';
                    }
                }
            }

            guess2 = "[" + guess2.substring(2) + "]";
            guess2 = JSON.parse(guess2);

            var x = new Date();

            bot.sendMessage({
                to: channelID,
                embed: {
                    color: 0x7289da,
                    author: {
                        name: '',
                    },
                    fields: guess2,
                    timestamp: x.toISOString(),
                    footer: {
                        text: "Last update"
                    }
                }
            });  
        });
    }

    if (message == "%ticket") {
        var u = "<@" + event.d.author.id + ">";

        bot.sendMessage({
            to: channelID,
            embed: {
                color: 3066993,
                author: {
                    name: "",
                },
                fields: [{
                        name: "Server message",
                        value: "Thanks for registering to our server, here's details for your ticket:",
                        inline: false
                      },{
                        name: "Username",
                        value: u,
                        inline: true
                      },{
                        name: "Ticket",
                        value: "Read <#311153138511904769>",
                        inline: true
                      }]
            }
        });
    }

    if (splitted['0'] == "$botinviteurl") {
        bot.sendMessage({
            to: userID,
            embed: {
                color: 15844367,
                author: {
                    name: "",
                },
                fields: [{
                    name: 'Use this link if you wanna invite ServerStatus bot to your server: ',
                    value: 'https://discordapp.com/oauth2/authorize?client_id=345657878763274242&scope=bot',
                    inline: true
                }]
            }
        });
    }

    if(message === "$ss") {
        var replyToDiscord = "";

        request({ url: "http://mirror.nfsw.mtntr.eu/stats/json.php" }, function(error, response, body) {
            if (!error && response.statusCode === 200) {
                serverreply = JSON.parse(body);

                for (var i = 0; i < serverreply.length; i++) {
                    if (serverreply[i] != "") {
                        var reply = serverreply[i];
                        if(reply.isOnline == "1") {
                            var online = "<:2010_on:430333566912692253> ";
                            var userstat = reply.onlineNumber + "/" + reply.registeredCount;
                        } else {
                            var online = "<:2010_off:430333566887395328> ";
                            var userstat = "---";
                        }

                        if(reply.requireTicket == "1") {
                            var replyreqtick = ":ticket: ";
                        } else {
                            var replyreqtick = "";
                        }

                        var countriezzz = reply.country;
                        countriezzz = countriezzz.split("/");
                        countriezzz = countriezzz['0'].toLowerCase();
                        if(countriezzz != "un") {
                            var flag = ':flag_' + countriezzz + ': ';
                        } else {
                            var flag = "";
                        }

                        if(reply.information) {
                            var information = "*(" + reply.information + ")* ";
                        } else {
                            var information = "";
                        }

                        console.log(reply.serverName + ": " + reply.country);

                        replyToDiscord += online + flag + replyreqtick + "**" + reply.serverName + "** [" + userstat + "] " + information + "\r\n";
                    }
                }
            } else {
                replyToDiscord = ":heavy_multiplication_x: Failed to fetch serverlist.";
            }

            var x = new Date();

            bot.sendMessage({
                to: channelID,
                embed: {
                    color: 0x7289da,
                    author: {
                        name: 'Actual serverlist status (launcher.soapboxrace.world)',
                        url: 'https://launcher.soapboxrace.world/stats/'
                    },
                    description: replyToDiscord,
                    timestamp: x.toISOString(),
                    footer: {
                        text: "Last update"
                    }
                }
            });
        });
    }
});
