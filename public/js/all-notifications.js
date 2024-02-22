let totalNotifications = 0
let cursor = ''
let notificationKeys = []
let notificationsList = []
let count = 0
let limit = 5;

const unreadCount = FIREBASE_INSTANCE.database().ref(FIREBASE_MASTER_PATH).orderByChild('isRead').equalTo(0)
unreadCount.on('value', snapshot => {
    count = snapshot.numChildren()
    getMoreCount('update', () => {
        renderLI();
    })
})


const getMoreCount = (type, callback) => {
    let currentType = type
    if (type == 'next') {
        const notificationsCount = FIREBASE_INSTANCE.database()
            .ref(FIREBASE_MASTER_PATH)
            .orderByKey()
            .endAt(cursor)
            .limitToLast(limit + 1);
        notificationsCount.on('value', snapshot => {
            if (currentType == 'next') {
                const notifications = snapshot.val()
                let notiList = [];
                let notiKeys = [];
                if (notifications) {
                    for (const [key, value] of Object.entries(notifications)) {
                        notiList.push(value)
                        notiKeys.push(key)
                    }
                    notiList.pop();
                    notiKeys.pop();
                    cursor = notiKeys[0];
                    notificationsList = [...notificationsList, ...(notiList.reverse())];
                    notificationKeys = [...notificationKeys, ...(notiKeys.reverse())];
                    if (notificationsList.length == totalNotifications)
                        cursor = notificationKeys[notificationKeys.length - 1]
                }
                currentType = ''
                if (callback) callback();
            }
        })
    } else if (type == 'first') {
        const notificationsCount = FIREBASE_INSTANCE.database()
            .ref(FIREBASE_MASTER_PATH)
            .orderByKey()
            .limitToLast(limit);
        notificationsCount.on('value', snapshot => {
            if (currentType == 'first') {
                const notifications = snapshot.val()
                let list = [];
                let keys = [];
                if (notifications) {
                    for (const [key, value] of Object.entries(notifications)) {
                        list.push(value)
                        keys.push(key)
                    }
                    cursor = keys[0];
                    notificationsList = list.reverse();
                    notificationKeys = keys.reverse();
                }
                currentType = ''
                if (callback) callback();
            }
        })
    } else if (type == 'update') {
        let startCursor = notificationKeys[0]
        const instance = FIREBASE_INSTANCE.database().ref(FIREBASE_MASTER_PATH)
            .orderByKey()
            .endAt(startCursor)
            .limitToLast(notificationsList.length);
        instance.on('value', snapshot => {
            if (currentType == 'update') {
                const notifications = snapshot.val()
                let notiList = [];
                let notiKeys = [];
                if (notifications) {
                    for (const [key, value] of Object.entries(notifications)) {
                        notiList.push(value)
                        notiKeys.push(key)
                    }
                    cursor = notiKeys[0];
                    notificationsList = [...(notiList.reverse())];
                    notificationKeys = [...(notiKeys.reverse())];
                }
                currentType = ''
                if (callback) callback();
            }
        })
    }
}

const totalCountRef = FIREBASE_INSTANCE.database().ref(FIREBASE_MASTER_PATH)
totalCountRef.on('value', snapshot => {
    totalNotifications = snapshot.numChildren()

    if (!cursor) {
        getMoreCount('first', () => {
            renderLI();
        })
    }
})

const markAllAsRead = (list, keys) => {
    list.map((notification, index) => {
        if (notification.isRead == 0) {
            let notificationId = keys[index]
            notification.isRead = 1
            /**Set key isRead as 1 in realtime. */
            const FIREBASE_ENDPOINT = `${FIREBASE_MASTER_PATH}/${notificationId}`
            const ref = firebase.database().ref(FIREBASE_ENDPOINT)
            ref.set(notification)
            // notificationsList[index] = notification
            // notificationsList = [...notificationsList];
        }
    })
    getMoreCount('update', () => {
        renderLI();
    })
}

const readNotification = (index, notificationDetails) => {
    const notificationId = notificationKeys[index]
    let notification = notificationsList[index]
    notification.isRead = 1
    // notificationsList[index] = notification
    // notificationsList = [...notificationsList]
    const FIREBASE_ENDPOINT = `${FIREBASE_MASTER_PATH}/${notificationId}`
    const ref = firebase.database().ref(FIREBASE_ENDPOINT)
    ref.set(notification)
    getMoreCount('update', () => {
        renderLI();
    })
}

const renderLI = () => {
    const titleInst = document.getElementById('count')
    titleInst.innerHTML = `(${count}) Notifications`
    const tableInst = document.getElementById('notifications_table')
    tableInst.innerHTML = ''
    let childNode = ''
    notificationsList.map((item, index) => {
        childNode += `<div class="nk-notification-item dropdown-inner" style=${item.isRead == 0 ? "background-color:#FFF;" : ""}>
                              <div class="nk-notification-icon">
                                <em
                                  class="icon icon-circle bg-warning-dim ni ni-curve-down-right"
                                ></em>
                              </div>
                              <div class="nk-notification-content">
                                <div class="nk-notification-text">
                                  <h6>${item.title} ${item.isRead == 0 ? `<a href="#" class="badge badge-primary badge-pill" id="nk-notification-${index}">Unread</a>` : ''}</h6>
                                </div>
                                <div class="nk-notification-text">
                                  ${item.body} 
                                </div>
                                <div class="nk-notification-time">2 hrs ago</div>
                                
                              </div>
                            </div> `;
    })
    tableInst.innerHTML = childNode;

    notificationsList.map((item, index) => {
        let btn = document.getElementById(`nk-notification-${index}`)
        if (btn) {
            btn.onclick = function () {
                readNotification(index, item)
            }
        }
    })

    const buttonInstance = document.getElementById('mark_all_as_read_1')
    buttonInstance.hidden = !count ? true : false
    buttonInstance.onclick = function () {
        markAllAsRead(notificationsList, notificationKeys)
    }
    const loadMoreInstance = document.getElementById('load-more');
    loadMoreInstance.hidden = notificationsList.length < totalNotifications ? false : true
    loadMoreInstance.onclick = function () {
        getMoreCount('next', () => {
            renderLI();

        })
        setTimeout(() => {
            renderLI()
        }, 1500)
    }
}

