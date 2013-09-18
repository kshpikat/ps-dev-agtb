({
    plugins: ['Dashlet', 'Timeago'],
    limit : 20,
    events: {
        'click .connect-twitter': 'onConnectTwitterClick'
    },
    initDashlet: function() {
        var limit = this.settings.get("limit") || this.limit;
            this.settings.set("limit", limit);
        this.cacheKey = "twitter.dashlet.current_user_cache";
        var currentUserCache = app.cache.get(this.cacheKey);
        if (currentUserCache && currentUserCache.current_twitter_user_name) {
            self.current_twitter_user_name = currentUserCache.current_twitter_user_name;
        }
        if (currentUserCache && currentUserCache.current_twitter_user_pic) {
            self.current_twitter_user_pic = currentUserCache.current_twitter_user_pic;
        }
    },
    onConnectTwitterClick: function(event) {
        if ( !_.isUndefined(event.currentTarget) ) {
            event.preventDefault();
            var href = this.$(event.currentTarget).attr('href');
            app.bwc.login(false, function(response){
                window.open(href);
            });
        }
    },
    _render: function () {
        if (this.tweets || this.meta.config) {
            app.view.View.prototype._render.call(this);
        }
    },
    bindDataChange: function(){
        if(this.model) {
            this.model.on("change", this.loadData, this);
        }
    },
    loadData: function (options) {
        if (this.disposed || this.meta.config) {
            return;
        }

        var twitter = this.settings.get('twitter') ||
                this.model.get('twitter') ||
                this.model.get('name') ||
                this.model.get('account_name') ||
                this.model.get('full_name'),
            limit = parseInt(this.settings.get("limit"), 10) || this.limit,
            self = this;
        this.screen_name = this.settings.get('twitter') || false;
        if (!twitter || this.viewName === 'config') {
            return false;
        }

        twitter = twitter.replace(" ", "");
        this.twitter = twitter;
        var currentUserUrl = app.api.buildURL('connector/twitter/currentUser');
        if (!self.current_twitter_user_name) {
            app.api.call('READ', currentUserUrl, {},{
                success: function(data) {
                    app.cache.set(self.cacheKey, {
                        'current_twitter_user_name': data.screen_name,
                        'current_twitter_user_pic': data.profile_image_url
                    });
                    self.current_twitter_user_name = data.screen_name;
                    self.current_twitter_user_pic = data.profile_image_url;
                    if (!this.disposed) {
                        self.render();
                    }
                }
            });
        }
        var url = app.api.buildURL('connector/twitter','',{id:twitter},{count:limit});
        app.api.call('READ', url, {},{
            success:function (data) {

                if (self.disposed) {
                    return;
                }

                var tweets = [];
                if (data.success !== false) {
                    _.each(data, function (tweet) {
                        var time = new Date(tweet.created_at.replace(/^\w+ (\w+) (\d+) ([\d:]+) \+0000 (\d+)$/,
                                "$1 $2 $4 $3 UTC")),
                            date = app.date.format(time, "Y/m/d H:i:s"),
                            // retweeted tweets are sometimes truncated so use the original as source text
                            text = tweet.retweeted_status ? 'RT @'+tweet.retweeted_status.user.screen_name+': '+tweet.retweeted_status.text : tweet.text,
                            sourceUrl = tweet.source,
                            id = tweet.id_str,
                            name = tweet.user.name,
                            tokenText = text.split(' '),
                            screen_name = tweet.user.screen_name,
                            profile_image_url = tweet.user.profile_image_url_https,
                            j,
                            rightNow = new Date(),
                            diff = (rightNow.getTime() - time.getTime())/(1000*60*60*24),
                            timeLabel= diff > 1 ? 'LBL_TIME_RELATIVE_TWITTER_LONG' : 'LBL_TIME_RELATIVE_TWITTER_SHORT';


                        // Search for links and turn them into hrefs
                        for (j = 0; j < tokenText.length; j++) {
                            if (tokenText[j].charAt(0) == 'h' && tokenText[j].charAt(1) == 't') {
                                tokenText[j] = "<a class='googledoc-fancybox' href=" + '"' + tokenText[j] + '"' + "target='_blank'>" + tokenText[j] + "</a>";
                            }
                        }

                        text = tokenText.join(' ');
                        tweets.push({id: id, name: name, screen_name: screen_name, profile_image_url: profile_image_url, text: text, source: sourceUrl, date: date, timeLabel: timeLabel});
                    }, this);
                }

                self.tweets = tweets;
                if (!this.disposed) {
                    self.render();
                }
            },
            error: function(xhr,status,error){
                if (xhr.status == 424) {
                    app.cache.cut(self.key);
                    self.needConnect = false;
                    if (xhr.code && xhr.code === 'ERROR_NEED_AUTHORIZE') {
                        self.needConnect = true;
                    } else if (xhr.code && xhr.code === 'ERROR_NEED_OAUTH') {
                        self.needOAuth = true;
                    } else {
                        self.showGeneric = true;
                    }
                    self.showAdmin = app.acl.hasAccess('admin', 'Administration');
                    self.template = app.template.get(self.name + '.twitter-need-configure.Home');
                    if (!self.disposed) {
                        app.view.View.prototype._render.call(self);
                    }
                }
            },
            complete: (options) ? options.complete : null
        });
    },
    _dispose: function() {
        if (this.model) {
            this.model.off("change", this.loadData, this);
        }

        app.view.View.prototype._dispose.call(this);
    }
})
