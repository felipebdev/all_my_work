import { StyleSheet, Text, View, Image, TextInput, TouchableOpacity, ActivityIndicator } from 'react-native'
import React, { useState } from 'react'
import { MaterialIcons } from '@expo/vector-icons';

const Esqueceuasenhaescolher = ({ navigation, route }) => {
    const { email } = route.params;
    const [senha, setsenha] = useState('')
    const [confirmarsenha, setconfirmarsenha] = useState('')
    const [loading, setLoading] = useState(false)


    const handlePasswordChange = () => {
        if (senha == '' || confirmarsenha == '') {
            alert('Por favor entre com um senha')
        } else if (senha != confirmarsenha) {
            alert('Senha não se coincidem')
        }

        else {
            setLoading(true);
            fetch('http://192.168.0.54:3000/resetsenha', {

                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email: email, senha: senha })
            })
                .then(res => res.json()).then(
                    data => {
                        if (data.message === "Senha alterada com sucesso") {
                            setLoading(false)
                            navigation.navigate('Esqueceuasenhaconfirmacao')
                        }
                        else {
                            setLoading(false)
                           
                        }
                    })
                .catch(err => {
                    setLoading(false);
                    alert(err)
                })
        }

   
    }
    return (
        <View style={{width: '100%', height: '100%'}}>

            <View style={{flexDirection: 'row', marginTop: 20}}>

                <Text style={{marginStart: 20, fontSize: 20, fontWeight: 'bold'}}>Ajuda para login</Text>
            <TouchableOpacity onPress={() => navigation.navigate('Login')} style={{marginStart: 185,
            marginTop: 10
            }}>

               <Image source={require('../../images/saircards.png')} style={{
                width: 15, height: 15
               }}/>

            </TouchableOpacity>

            </View>

        
            <Text style={{fontWeight: 'bold', marginTop: 40, alignSelf: 'center',
        fontSize: 15, width: 350, textAlign: 'center', color: 'gray'}} 
        >Escolha uma senha nova</Text>

            <TextInput placeholder="Entre com sua senha nova"  secureTextEntry
                onChangeText={(text) => setsenha(text)}
                style={{width: '95%', backgroundColor: 'white', height: 40, alignSelf: 'center',
                marginTop: 30, borderRadius: 5, padding: 10}}
            />
            <TextInput placeholder="Confirme sua nova senha"  secureTextEntry
                onChangeText={(text) => setconfirmarsenha(text)}
                style={{width: '95%', backgroundColor: 'white', height: 40, alignSelf: 'center',
                marginTop: 10, borderRadius: 5, padding: 10}}
            />
            {
                loading ? <ActivityIndicator size="large" color="white" style={{marginTop: 10}} /> :
                <TouchableOpacity style={{backgroundColor: '#ec230d', width: '95%', 
                justifyContent: 'center', alignItems: 'center', alignSelf: 'center', marginTop: 10,
                height: 40, borderRadius: 5}} onPress={() => handlePasswordChange()}>

                    <Text style={{color: 'white', fontWeight: 'bold'}}>
                        Avançar
                    </Text>
                    </TouchableOpacity>
            }

        </View>
    )
}



export default Esqueceuasenhaescolher

const styles = StyleSheet.create({})