import {
    StyleSheet, Text, View, StatusBar, ScrollView, Image,
     ActivityIndicator, TouchableOpacity, TextInput
} from 'react-native'
import { LinearGradient } from 'expo-linear-gradient';
import React, { useEffect, useRef } from 'react'
import { MaterialIcons } from '@expo/vector-icons';
import io from 'socket.io-client'
import AsyncStorage from '@react-native-async-storage/async-storage';

const socket = io('http://192.168.0.54:3001')


const Mensagem = ({ navigation, route }) => {

    const { fuseremail, fuserid } = route.params;

    const [ouruserdata, setOuruserdata] = React.useState(null);
    const [fuserdata, setFuserdata] = React.useState(null);

    const [userid, setUserid] = React.useState(null);
    const [roomid, setRoomid] = React.useState(null);
    const [chat, setChat] = React.useState(['']);

    // OUR ID & ROOM ID FOR SOCKET.IO

    useEffect(() => {
        loaddata()
    }, [])

    useEffect(() => {
        socket.on('receive_message', (data) => {
            
            loadMessages(roomid)
        })
    }, [socket])


    const sortroomid = (id1, id2) => {
        if (id1 > id2) {
            return id1 + id2
        } else {
            return id2 + id1
        }
    }


    const loaddata = async () => {
        AsyncStorage.getItem('user')
            .then(async (value) => {
                fetch('http://192.168.0.54:3000/userdata', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + JSON.parse(value).token
                    },
                    body: JSON.stringify({ email: JSON.parse(value).user.email })
                })
                    .then(res => res.json()).then(data => {
                        if (data.message == 'Usuário Encontrado') {
                            // console.log('our user data ', data.user.username)
                            setOuruserdata(data.user)
                            setUserid(data.user._id)

                            fetch('http://192.168.0.54:3000/outrosusuarios', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({ email: fuseremail })
                            })
                                .then(res => res.json())
                                .then(async data1 => {
                                    if (data1.message == 'Usuário Encontrado') {
                                        // console.log('fuser data ', data1.user.username)
                                        setFuserdata(data1.user)
                                        let temproomid = await sortroomid(fuserid, data.user._id)

                                        setRoomid(temproomid)
                                        // console.log('room id ', temproomid)
                                        socket.emit('join_room', { roomid: temproomid })
                                        loadMessages(temproomid)
                                    }
                                    else {
                                        alert('Usuário não encontrado')
                                        navigation.navigate('Pesquisar')
                                        // navigation.navigate('Login')
                                    }
                                })
                                .catch(err => {
                                    // console.log(err)
                                   
                                    navigation.navigate('Pesquisar')
                                })
                        }
                        else {
                           
                            navigation.navigate('Login')
                        }
                    })
                    .catch(err => {
                        navigation.navigate('Login')
                    })
            })
            .catch(err => {
                navigation.navigate('Login')
            })
    }

    // const joinroom = () => {
    //     socket.emit('join_room', { roomid: roomid })
    // }

    const sendMessage = async () => {
        const messagedata = {
            message: currentmessage,
            roomid: roomid,
            senderid: userid,
            recieverid: fuserdata._id
        }
        fetch('http://192.168.0.54:3000/salvamensagemdb', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(messagedata)
        }).then(res => res.json())
            .then(data => {
                if (data.message == 'Mensagem salva com sucesso') {

                    socket.emit('send_message', messagedata)
                    loadMessages(roomid)
                  

                   

                    setCurrentmessage('')

                }
                else {
                 
                    setCurrentmessage('')
                }
            })
            .catch(err => {
                console.log(err)
            })
    }

    useEffect(() => {
        loadMessages(roomid)
    }, [chat])

    const [currentmessage, setCurrentmessage] = React.useState(null);


    const loadMessages = (temproomid) => {
        fetch('http://192.168.0.54:3000/getmessages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ roomid: temproomid })
        }).then(res => res.json())
            .then(data => {
                setChat(data)
            })
    }
    const scrollViewRef = useRef();
    return (
        <View style={styles.container}>
            <View style={styles.s1}>
            <LinearGradient colors={['#ec230d', '#f75738', '#be6049']} start={{x: 2, y: 0.15}} style={{   width: '100%',
        alignItems: 'center',
        flexDirection: 'row',
        height: '100%'}}>
                <TouchableOpacity onPress={() => navigation.goBack()} style={styles.goback}>
                    <MaterialIcons name="arrow-back-ios" size={27} color="white" style={{marginStart: 10}} />
                </TouchableOpacity>

                {
                    fuserdata?.profilepic ?
                        <Image source={{ uri: fuserdata?.profilepic }} style={styles.profilepic} />
                        :
                        <Image source={('./../../images/nopic.png')} style={styles.profilepic} />

                }
                <Text style={styles.username}>{fuserdata?.nome}</Text>

                <TouchableOpacity style={{marginStart: 15}}>
                <Image source={require('../../images/ligacao.png')} style={{width: 25, height: 25, tintColor: 'white'}}/>
                </TouchableOpacity>

                <TouchableOpacity style={{marginStart: 20}}>
                <Image source={require('../../images/chamadadevideo.png')} style={{width: 30, height: 20, tintColor: 'white'}}/>
                </TouchableOpacity>
                </LinearGradient>
            </View>



            <ScrollView style={styles.messageView}
                ref={scrollViewRef}
                onContentSizeChange={() => scrollViewRef.current.scrollToEnd({ animated: true })}
            >
                {
                    chat.map((item, index) => {
                        return (
                            <View style={styles.message} key={index}>
                                {
                                    item.senderid == userid &&
                                    <View style={styles.messageRight}>
                                        <LinearGradient colors={['#ec230d', '#f75738', '#be6049']} start={{x: 2, y: 0.15}}
                                        style={{   minWidth: 100,
                                            padding: 10,
                                            fontSize: 17,
                                            borderRadius: 20,
                                            margin: 10,}}>
                                        <Text style={styles.messageTextRight}>{item.message}</Text>
                                        </LinearGradient>
                                    </View>
                                }
                                {
                                    item.senderid != userid && item != '' &&
                                    <View style={styles.messageLeft}>
                                        <Text style={styles.messageTextLeft}>{item.message}</Text>
                                    </View>
                                }
                            </View>
                        )
                    })
                }
            </ScrollView>


            <View style={{  width: '100%', height: 50, position: 'absolute',bottom: 0,}}>
            <LinearGradient colors={['#ec230d', '#f75738', '#be6049']} start={{x: 2, y: 0.15}} style={styles.sbottom}>

                <TouchableOpacity>
                    <Image source={require('../../images/emoji.png')} style={{width: 25, height: 25,
                    tintColor: 'white'}}/>
                </TouchableOpacity>

                <TextInput style={styles.sbottominput} placeholder='Escreva uma mensagem'
                    placeholderTextColor={'white'}
                    onChangeText={(text) => setCurrentmessage(text)}
                    value={currentmessage}
                />
                <TouchableOpacity style={styles.sbottombtn}>
                    {
                        currentmessage ?
                            <MaterialIcons name="send" size={24} color="white"
                                onPress={() => sendMessage()}
                            /> :
                            <MaterialIcons name="send" size={27} color="white" />
                    }


                </TouchableOpacity>
                </LinearGradient>
            </View>
        </View>
    )
}

export default Mensagem

const styles = StyleSheet.create({
    container: {
        width: '100%',
        height: '100%',
        backgroundColor: 'white',
    },
    profilepic: {
        width: 40,
        height: 40,
        borderRadius: 25,
    },
    username: {
        color: 'white',
        fontSize: 20,
        marginLeft: 10,
        fontWeight: 'bold',
        width: 200,
    },
    s1: {
        width: '100%',
        height: 50,
    },
    sbottom: {
        width: '100%',
        height: '100%',
        flexDirection: 'row',
        alignSelf: 'center',
        justifyContent: 'space-between',
        padding: 10,
        position: 'absolute',
        bottom: 0,
        borderRadius: 0,
        alignItems: 'center'
    },
    sbottominput: {
        width: '80%',
        height: 40,
        fontSize: 17,
        color: 'white',
        
    },

    message: {
        width: '100%',
        // padding:10,
        borderRadius: 10,
        // marginVertical:5,
        // backgroundColor:'red',
    },
    messageView: {
        width: '100%',
        marginBottom: 50,
    },
    messageRight: {
        width: '100%',
        alignItems: 'flex-end',
        // backgroundColor:'red'
    },
    messageTextRight: {
        color: 'white',
        // width:'min-content',
        fontSize: 17,
    },
    messageLeft: {
        width: '100%',
        alignItems: 'flex-start',
        // backgroundColor:'red'
    },
    messageTextLeft: {
        color: 'white',
        backgroundColor: '#222222',
        fontSize: 17,
        minWidth: 100,
        padding: 10,
        borderRadius: 20,
        margin: 10,
    },
})