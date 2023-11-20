import { ScrollView, StyleSheet, Text, TextInput, View } from 'react-native'
import React, { useEffect, useState } from 'react'
import { MaterialIcons } from '@expo/vector-icons';
import ChatCard from '../Cards/Usercard';
import AsyncStorage from '@react-native-async-storage/async-storage';

const Todoschats = ({ navigation }) => {
 

    const [chats, setChats] = useState(null)

    const [keyword, setKeyword] = useState('')


    const [userdata, setUserdata] = useState(null)
    useEffect(() => {
        loadchats()
    }, [])
    const loadchats = () => {
        AsyncStorage.getItem('user')
            .then(data => {
               
                setUserdata(JSON.parse(data))
                let userid = JSON.parse(data).user._id;


                fetch('http://192.168.0.54:3000/getusermessages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        userid: userid
                    })
                })
                    .then(res => res.json())
                    .then(data => {
                        // console.log(data)
                        data.sort((a, b) => {
                            if(a.date > b.date){
                                return -1
                            }
                        })
                        setChats(data)
                    })
                    .catch(err => {
                        setChats([])
                    })
            })
            .catch(err => alert(err))
    }
    return (
        <ScrollView style={styles.container}>
            <View style={{flexDirection: 'row'}}>
           <MaterialIcons name="arrow-back-ios" size={27} color="#ec230d"
                style={{marginStart: 20, marginTop: 20}}
                onPress={() => navigation.navigate('Homescreen')}
            />


            </View>

            <View style={styles.c1}>
               
                <TextInput placeholder="Pesquisar"
                style={{width: 350, height: 50, backgroundColor: 'white', padding: 10, borderRadius: 10, fontSize: 17}}
                    onChangeText={(text) => setKeyword(text)}
                />
            </View>

            <View style={styles.c2}>
                {
                    chats!==null && chats.filter(
                        (chat) => {
                            if (keyword == '') {
                                return chat
                            }
                            else if (
                                chat.nome.toLowerCase().includes(keyword.toLowerCase())
                                ||
                                chat.lastmessage.toLowerCase().includes(keyword.toLowerCase())
                            ) {
                                return chat
                            }
                        }
                    ).map((chat) => {
                        return <ChatCard key={chat.fuserid} chat={chat} navigation={navigation}/>
                    })
                }
            </View>
        </ScrollView>
    )
}

export default Todoschats

const styles = StyleSheet.create({
    container: {
        width: '100%',
        height: '100%',
        backgroundColor: '#fafafa',
    },
    gohomeicon: {
        position: 'absolute',
        top: 15,
        left: 20,
        zIndex: 10,
        color: 'white',
        fontSize: 30,
    },
    c1: {
        alignItems: 'center',
        justifyContent: 'center',
        marginTop: 20
    },
    searchbar: {
        width: '90%',
        backgroundColor: 'white',
        borderRadius: 30,
        paddingVertical: 10,
        paddingHorizontal: 20,
        marginTop: 10,
        fontSize: 18,
    },
    c2: {
        width: '100%',
        padding: 10,
    }

})

