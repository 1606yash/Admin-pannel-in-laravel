

var firebaseConfig = {
    apiKey: 'AIzaSyCAXligEQ4j0_XwC4Xp6jTtcf0zBvreZHA',
    authDomain: 'profitley.firebaseapp.com',
    databaseURL: 'https://profitley-default-rtdb.firebaseio.com',
    projectId: 'profitley',
    storageBucket: 'profitley.appspot.com',
    messagingSenderId: '607534301528',
    appId: '1:607534301528:web:42fe87b7362d788a47252d',
    measurementId: 'G-JP5P44D84L'
}
// Initialize Firebase
const FIREBASE_INSTANCE = !firebase.apps.length
    ? firebase.initializeApp(firebaseConfig)
    : firebase.app()
const FIREBASE_MASTER_PATH = '/notifications/user_id_'+LOGIN_USER_ID;



const topNotifications = FIREBASE_INSTANCE.database()
    .ref(FIREBASE_MASTER_PATH)
    .orderByKey();
// const unreadNotifications = FIREBASE_INSTANCE.database()
//     .ref(FIREBASE_MASTER_PATH)
//     .orderByChild('isRead')
//     .equalTo(0)


// unreadNotifications.on('value', snapshot => {
//     const notifications = snapshot.val()
//     count = 0;
//     if (notifications) {
//         for (const [key, value] of Object.entries(notifications)) {
//             count++
//         }
//     }
// });

topNotifications.on('value', snapshot => {
    const notifications = snapshot.val()
    let topNotificationKeys = []
    let topNotificationsList = []
    let count = 0;

    if (notifications) {
        for (const [key, value] of Object.entries(notifications)) {
            if (value.isRead == 0) count++
            topNotificationsList.push(value)
            topNotificationKeys.push(key)
        }
        topNotificationsList = topNotificationsList.reverse();
        topNotificationsList = topNotificationsList.filter((n, index) => index < 10);
        topNotificationKeys = topNotificationKeys.reverse();
        topNotificationKeys = topNotificationKeys.filter((n, index) => index < 10)
    }

    const markAllAsRead = () => {
        topNotificationsList.map((notification, index) => {
            const notificationId = topNotificationKeys[index]
            notification.isRead = 1
            /**Set key isRead as 1 in realtime. */
            const FIREBASE_ENDPOINT = `${FIREBASE_MASTER_PATH}/${notificationId}`
            const ref = firebase.database().ref(FIREBASE_ENDPOINT)
            ref.set(notification)
        })
    }

    const readNotification = (index, notificationDetails) => {
        const notificationId = topNotificationKeys[index]
        let notification = topNotificationsList[index]
        notification.isRead = 1
        const FIREBASE_ENDPOINT = `${FIREBASE_MASTER_PATH}/${notificationId}`
        const ref = firebase.database().ref(FIREBASE_ENDPOINT)
        ref.set(notification)
    }

    const buttonInstance = document.getElementById('mark_all_as_read')
    buttonInstance.hidden = !count ? true : false
    buttonInstance.onclick = function () {
        markAllAsRead()
    }

    const bellInst = document.getElementById('ni-bell')
    bellInst.innerHTML = count ? `${count}` : ''

    const tableInst = document.getElementById('nk-notification')
    tableInst.innerHTML = ''
    let childNode = ''
    topNotificationsList.map((item, index) => {
        childNode += `<div class="nk-notification-item dropdown-inner" style=${item.isRead == 0 ? "" : "background-color:#E5E9F2;"}>
                              <div class="nk-notification-icon">
                                <em
                                  class="icon icon-circle bg-warning-dim ni ni-curve-down-right"
                                ></em>
                              </div>
                              <div class="nk-notification-content">
                                <div class="nk-notification-text">
                                  ${item.title}
                                </div>
                                <div class="nk-notification-time">
                                  ${item.body} 
                                </div>
                                ${item.isRead == 0 ? `<div>
                <button type="button" id="nk-btn-${index}">Mark as read</button>
            </div>` : ''}
                              </div>
                            </div> `;
    })
    tableInst.innerHTML = childNode;
    topNotificationsList.map((item, index) => {
        let btn = document.getElementById(`nk-btn-${index}`)
        if (btn) {
            btn.onclick = function () {
                readNotification(index, item)
            }
        }
    })
})

